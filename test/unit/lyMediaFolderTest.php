<?php
include(dirname(__FILE__).'/../bootstrap/unit.php');

$conn = lyMediaFolderTable::getInstance()
  ->getConnection();

$root = lyMediaFolderTable::getInstance()
  ->createRoot('test_root');

$fs = new lyMediaFileSystem();

$t = new lime_test(15, new lime_output_color());

$t->info('Create first level folder');
$folder = new lyMediaFolder();
$folder->setName('test');
$folder->create($root);
$folder->refresh();

/*
 * test_root
 *  -- test
 */
$t->is($folder->getName(), 'test', '->getName()');
$t->is($folder->getRelativePath(), 'test_root/test/', '->getRelativePath()');
$t->ok($folder->getNode()->isValidNode(), 'Folder is a valid node');
$t->is($folder->getNode()->getPath('/', true), 'test_root/test', 'Folder has right path');
$t->ok($fs->is_dir($folder->getRelativePath()), 'Folder exists in filesystem');

$t->info('Create sub-folder');
$sub = new lyMediaFolder();
$sub->setName('test-sub');
$sub->create($folder);
$sub->refresh();

/*
 * test_root
 *  -- test
 *  -- -- test-sub
 */
$t->is($sub->getRelativePath(), 'test_root/test/test-sub/', '->getRelativePath()');
$t->ok($sub->getNode()->isValidNode(), 'Folder is a valid node');
$t->is($sub->getNode()->getPath('/', true), 'test_root/test/test-sub', 'Subfolder has right path');
$t->ok($fs->is_dir($sub->getRelativePath()), 'Folder exists in filesystem');

$folder2 = new lyMediaFolder();
$folder2->setName('test2');
$folder2->create($root);
$folder2->refresh();

/*
 * test_root
 *  -- test
 *  -- -- test-sub
 *  -- test 2
 */
$t->info('Move folder');
$folder2->move($folder);
$folder2->refresh();

/*
 * test_root
 *  -- test
 *  -- -- test 2
 *  -- -- test-sub
 */
$t->is($folder2->getRelativePath(), 'test_root/test/test2/', '->getRelativePath()');
$t->is($folder2->getNode()->getPath('/', true), 'test_root/test/test2', 'Folder has right path');
$t->ok($fs->is_dir($folder2->getRelativePath()), 'Folder exists in filesystem');

$t->info('Move folder deeper');
$folder2->move($sub);
$folder2->refresh();

/*
 * test_root
 *  -- test
 *  -- -- test-sub
 *  -- -- -- test 2
 */
$t->is($folder2->getRelativePath(), 'test_root/test/test-sub/test2/', '->getRelativePath()');
$t->is($folder2->getNode()->getPath('/', true), 'test_root/test/test-sub/test2', 'Folder has right path');
$t->ok($fs->is_dir($folder2->getRelativePath()), 'Folder exists in filesystem');
