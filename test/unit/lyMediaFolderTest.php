<?php
include(dirname(__FILE__).'/../bootstrap/unit.php');

$conn = Doctrine::getTable('lyMediaFolder')
  ->getConnection();

$root = Doctrine::getTable('lyMediaFolder')
  ->createRoot('test_root');

$t = new lime_test(15, new lime_output_color());

$t->info('Create first level folder');
$folder = new lyMediaFolder();
$folder->setName('test');
$folder->create($root);
$folder->refresh();

$t->is($folder->getName(), 'test', '->getName()');
$t->is($folder->getRelativePath(), 'test_root/test/', '->getRelativePath()');
$t->ok($folder->getNode()->isValidNode(), 'Folder is a valid node');
$t->is($folder->getNode()->getPath('/', true), 'test_root/test', 'Folder has right path');
$t->ok(is_dir(lyMediaTools::getBasePath() . $folder->getRelativePath()), 'Folder exists in filesystem');

$t->info('Create sub-folder');
$sub = new lyMediaFolder();
$sub->setName('test-sub');
$sub->create($folder);
$sub->refresh();

$t->is($sub->getRelativePath(), 'test_root/test/test-sub/', '->getRelativePath()');
$t->ok($sub->getNode()->isValidNode(), 'Folder is a valid node');
$t->is($sub->getNode()->getPath('/', true), 'test_root/test/test-sub', 'Subfolder has right path');
$t->ok(is_dir(lyMediaTools::getBasePath() . $sub->getRelativePath()), 'Folder exists in filesystem');

$folder2 = new lyMediaFolder();
$folder2->setName('test2');
$folder2->create($root);
$folder2->refresh();

$t->info('Move folder');
$folder2->move($folder);
$folder2->refresh();

$t->is($folder2->getRelativePath(), 'test_root/test/test2/', '->getRelativePath()');
$t->is($folder2->getNode()->getPath('/', true), 'test_root/test/test2', 'Folder has right path');
$t->ok(is_dir(lyMediaTools::getBasePath() . $folder2->getRelativePath()), 'Folder exists in filesystem');

$t->info('Move folder deeper');
$folder2->move($sub);
$folder2->refresh();

$t->is($folder2->getRelativePath(), 'test_root/test/test-sub/test2/', '->getRelativePath()');
$t->is($folder2->getNode()->getPath('/', true), 'test_root/test/test-sub/test2', 'Folder has right path');
$t->ok(is_dir(lyMediaTools::getBasePath() . $folder2->getRelativePath()), 'Folder exists in filesystem');
