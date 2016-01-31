<?php
/**
 * Created by PhpStorm.
 * User: yonman
 * Date: 28/01/2016
 * Time: 8:52 AM
 */

namespace GitLogVersion\Command;


use Cilex\Command\Command;
use Clue\PharComposer\Phar\Packager;
use GitLogVersion\GitLog;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends Command {

    /**
     * @var string
     */
    private $path;

    public function __construct($name) {
        $this->path = $name;
        parent::__construct(null);
    }

    protected function configure() {
        $this
            ->setName('build')
            ->setDescription('Build the project into git-log-version.phar executable');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {


        $packager = new Packager();
        $packager->setOutput(function ($line) use ($output) {
            $output->write($line);
        });

        $packager->coerceWritable();
        $pharer = $packager->getPharer($this->path);
        $pharer->setTarget($this->path . '/git-log-version.phar');
        $pharer->setMain($this->path . '/index.php');
        $pharer->build();

        chmod($this->path . '/git-log-version.phar', 0755);
    }

}