<?php

include(dirname(__FILE__).'/../bootstrap/unit.php');

$fs = new lyMediaFileSystem();
$t = new lime_test(5, new lime_output_color());

$t->info('->createRoot()');
$root = Doctrine::getTable('lyMediaFolder')
  ->createRoot('test_root');

$t->is($root->getRelativePath(), 'test_root/', '->getRelativePath()');
$t->ok($root->getNode()->isValidNode(), 'Folder is valid node');
$t->ok($root->getNode()->isRoot(), 'Folder is root');
$t->ok($fs->is_dir($root->getRelativePath()), 'Root dir created in filesystem');
$t->info('->getRoot()');
$t->is_deeply($root->toArray(), Doctrine::getTable('lyMediaFolder')->getRoot()->toArray(), '->getRoot()');
