#!/usr/bin/php
<?php
/**
 * Created by PhpStorm.
 * User: yonman
 * Date: 27/01/2016
 * Time: 6:09 PM
 */

if (!$loader = include __DIR__ . '/vendor/autoload.php') {
    die('You must set up the project dependencies.');
}

$app = new \Cilex\Application('Cilex');
$app->command(new \GitLogVersion\Command\ReportCommand());
$app->command(new \GitLogVersion\Command\LabelCommand());
$app->command(new \GitLogVersion\Command\CommentCommand());
$app->command(new \GitLogVersion\Command\BuildCommand(__DIR__));
$app->run();