<?php
/**
 * Created by PhpStorm.
 * User: yonman
 * Date: 28/01/2016
 * Time: 8:52 AM
 */

namespace GitLogVersion\Command;


use Cilex\Command\Command;
use GitLogVersion\GitLog;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReportCommand extends Command {

    protected function configure() {
        $this
            ->setName('report')
            ->setDescription('Display a git log report for the current workspace')
            ->addArgument('start-range', InputArgument::REQUIRED, 'The starting tag/commit/branch-head of the commit range')
            ->addArgument('and-or-end-range', InputArgument::REQUIRED, 'The ending tag/commit/branch-head of the commit range')
            ->addArgument('end-range', InputArgument::OPTIONAL, 'The ending tag/commit/branch-head of the commit range');
//            ->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $startRange = $input->getArgument('start-range');
        $endRange = $input->getArgument('end-range') ? $input->getArgument('end-range') : $input->getArgument('and-or-end-range');

        $gitLog = new GitLog();
        $entries = $gitLog->report($startRange, $endRange);

        $output->writeln($entries);
    }

}