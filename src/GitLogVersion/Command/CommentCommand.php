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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Tivie\Command\Exception\Exception as CommandException;

class CommentCommand extends Command {

    private $projects = [1193134, 1431906, 1349714, 1269932];

    protected function configure() {
        $this
            ->setName('comment')
            ->setDescription('Add a comment onto story IDs found in the log range')
            ->addOption('input-file', 'i', InputOption::VALUE_OPTIONAL, 'File path to the comment string');
    }


    protected function execute(InputInterface $input, OutputInterface $output) {

        if ((! $input->hasOption('input-file')) || (!$input->getOption('input-file'))) {
            throw new \Exception('input-file filepath option must be specified');
        }
        $commentFilePath = $input->getOption('input-file');
        if (! file_exists($commentFilePath)) {
            throw new \Exception("$commentFilePath not found");
        }

        $commentText = file_get_contents($commentFilePath);

        $client = new Client();
        $request = $client->createRequest('GET', 'https://www.pivotaltracker.com/services/v5/me', array('X-TrackerToken' => 'be17fcf368af9fa35cfe88b7460d2c67'));
        $response = $client->send($request);

        $this->projects = array_map(function($item){
            return $item['project_id'];
        }, json_decode($response->getBody(), true)['projects']);

        $labelsList = implode(',', $this->projects);

        $extractor = new StoriesExtractor();
        $storyIds = $extractor->collect();

        $storyCount = count($storyIds);
        $output->writeln("Adding comments on $storyCount stories on projects $labelsList");

        foreach ($storyIds as $storyId) {
            foreach ($this->projects as $project) {

                $request = $client->createRequest('POST',
                    "https://www.pivotaltracker.com/services/v5/projects/$project/stories/$storyId/comments");

                $request->setHeader('X-TrackerToken', 'be17fcf368af9fa35cfe88b7460d2c67');
                $request->setHeader('Content-type', 'application/json');
                $request->setHeader('Accept', 'application/json');

                $request->setBody(json_encode(['text' => $commentText]));

                try {
                    $client->send($request);
                    $output->write('.');
                    break;
                } catch (ClientErrorResponseException $ex) {
                    $output->writeln('Could not comment on story ' . $storyId);
                    $output->writeln($ex->getResponse()->getBody(true),true);
                }
            }
        }
        $output->writeln('');
    }

}