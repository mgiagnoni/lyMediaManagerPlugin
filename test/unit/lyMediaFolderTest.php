<?php
include(dirname(__FILE__).'/../bootstrap/unit.php');

$conn = lyMediaFolderTable::getInstance()
  ->getConnection();

$root = lyMediaFolderTable::getInstance()
  ->createRoot('test_root');

$fs = new lyMediaFileSystem();

$t = new lime_test(30, new lime_output_color());

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

$t->info('Synchronize folder');
//Create orphaned asset
$asset = new lyMediaAsset();
$asset->setFolder($folder);
$asset->setFilename(dirname(__FILE__) . '/../data/assets/asseta.png');
$asset->save();

$root->synchronizeWith(dirname(__FILE__) . '/../data/assets/',false);

$asset = lyMediaAssetTable::getInstance()
  ->findOneByFilename('asset1.png');
$t->ok(is_object($asset), 'Asset created in root');
$t->is($asset->getPath(), 'test_root/asset1.png', 'Asset has correct path');
$t->ok($fs->is_file('test_root/asset1.png'), 'Asset exists in filesystem');
$t->ok($fs->is_file('test_root/thumbs/small_asset1.png'), 'Small thumbnail exists in filesystem');
$t->ok($fs->is_file('test_root/thumbs/medium_asset1.png'), 'Medium thumbnail exists in filesystem');

$asset = lyMediaAssetTable::getInstance()
  ->findOneByFilename('assetc.png');
$t->ok(is_object($asset), 'Asset created in subfolder');
$t->is($asset->getPath(), 'test_root/test/test-sub/assetc.png', 'Asset has correct path');
$t->ok($fs->is_file('test_root/test/test-sub/assetc.png'), 'Asset exists in filesystem');
$t->ok($fs->is_file('test_root/test/test-sub/thumbs/small_assetc.png'), 'Small thumbnail exists in filesystem');
$t->ok($fs->is_file('test_root/test/test-sub/thumbs/medium_assetc.png'), 'Medium thumbnail exists in filesystem');
$t->ok($fs->is_dir('test_root/test/test-sub/test2'), 'Orphaned folder still exists');
$t->ok($fs->is_file('test_root/test/asseta.png'), 'Orphaned asset still exists');

$t->info('Synchronize folder (remove orphaned folder)');
$root->synchronizeWith(dirname(__FILE__) . '/../data/assets/',false,false,true);
$t->ok(!$fs->is_dir('test_root/test/test-sub/test2'), 'Orphaned folder no longer exists');
$t->ok($fs->is_file('test_root/test/asseta.png'), 'Orphaned asset still exists');
$t->info('Synchronize folder (remove orphaned asset)');
$root->synchronizeWith(dirname(__FILE__) . '/../data/assets/',false,true,false);
$t->ok(!$fs->is_file('test_root/test/asseta.png'), 'Orphaned asset no longer exists');