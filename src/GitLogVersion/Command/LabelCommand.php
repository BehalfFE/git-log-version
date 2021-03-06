<?php
/**
 * Created by PhpStorm.
 * User: yonman
 * Date: 28/01/2016
 * Time: 8:52 AM
 */

namespace GitLogVersion\Command;


use Cilex\Command\Command;
use GitLogVersion\StoriesExtractor;
use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException as ClientErrorResponseException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tivie\Command\Exception\Exception as CommandException;

class LabelCommand extends Command {

    private $projects = [1193134, 1431906, 1349714, 1269932];

    protected function configure() {
        $this
            ->setName('label')
            ->setDescription('Add the specified label onto story IDs found in the log range')
            ->addArgument('label', InputArgument::REQUIRED, 'The starting tag/commit/branch-head of the commit range');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $label = $input->getArgument('label');

        $extractor = new StoriesExtractor();
        $storyIds = $extractor->collect();

        $client = new Client();
        $request = $client->createRequest('GET', 'https://www.pivotaltracker.com/services/v5/me', array('X-TrackerToken' => 'be17fcf368af9fa35cfe88b7460d2c67'));
        $response = $client->send($request);

        $this->projects = array_map(function($item){
            return $item['project_id'];
        }, json_decode($response->getBody(), true)['projects']);

        $labelsList = implode(',', $this->projects);
        $storyCount = count($storyIds);
        $output->writeln("Labeling $storyCount stories on projects $labelsList");

        foreach ($storyIds as $storyId) {
            foreach ($this->projects as $project) {

                $request = $client->createRequest('GET',
                    "https://www.pivotaltracker.com/services/v5/projects/$project/stories/$storyId",
                    ['X-TrackerToken' => 'be17fcf368af9fa35cfe88b7460d2c67']);

//                $request->setHeader§('X-TrackerToken', 'be17fcf368af9fa35cfe88b7460d2c67');
                try {
                    $storyData = $client->send($request);
                } catch (ClientErrorResponseException $ex) {
                    continue;
                }

                $body = $storyData->getBody(true);
                $storyData = json_decode($body, true);
                $labels = $storyData['labels'];
                $labels[] = ['name' => $label];

                $request = $client->createRequest('PUT',
                    "https://www.pivotaltracker.com/services/v5/projects/$project/stories/$storyId");

                $request->setHeader('X-TrackerToken', 'be17fcf368af9fa35cfe88b7460d2c67');
                $request->setHeader('Content-type', 'application/json');
                $request->setHeader('Accept', 'application/json');

                $request->setBody(json_encode(['labels' => $labels]));

                try {
                    $client->send($request);
                    $output->write('.');
                    break;
                } catch (ClientErrorResponseException $ex) {
                    $output->writeln('Could not label story ' . $storyId);
                    $output->writeln($ex->getResponse()->getBody(true),true);
                }
            }
        }
        $output->writeln('');
    }

}