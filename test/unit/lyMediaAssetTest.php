<?php
include(dirname(__FILE__).'/../bootstrap/unit.php');

//Create folders for tests
$root = Doctrine::getTable('lyMediaFolder')
  ->createRoot('test_root');

$folder = new lyMediaFolder();
$folder->setName('test');
$folder->create($root);

$base = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'test_root' . DIRECTORY_SEPARATOR;

$t = new lime_test(31, new lime_output_color());

$t->info('Create asset');
$file = dirname(__FILE__) . '/../data/assets/asset1.png';
$a = new lyMediaAsset();
$a->setFolder($root);
$a->setFilename($file);
$a->save();
$a->refresh();

$t->is($a->getFilename(), 'asset1.png', '->getFilename()');
$t->is($a->getType(), 'image/png', '->getType()');
$t->is($a->getPath(), 'test_root/asset1.png', '->getPath()');
$t->ok(file_exists($base . 'asset1.png'), 'File exists');
$t->ok(file_exists($base .  'thumbs/small_asset1.png'), 'Small thumbnail exists');
$t->ok(file_exists($base . 'thumbs/medium_asset1.png'), 'Medium thumbnail exists');

$t->info('Create another asset with the same file');

$a2 = new lyMediaAsset();
$a2->setFolder($root);
$a2->setFilename($file);
$a2->save();
$a2->refresh();

$t->is($a2->getFilename(), 'asset1(1).png', 'Filename is unique');

$t->info('Rename asset: asset1.png > renamed.png');

$a->setFilename('renamed.png');
$a->save();
$a->refresh();

$t->is($a->getPath(), 'test_root/renamed.png', '->getPath()');
$t->ok(file_exists($base . 'renamed.png'), 'Renamed file exists');
$t->ok(file_exists($base . 'thumbs/small_renamed.png'), 'Small thumbnail is renamed');
$t->ok(file_exists($base . 'thumbs/medium_renamed.png'), 'Medium thumbnail is renamed');
$t->ok(!file_exists($base . 'asset1.png'), 'Old file name does not exist');
$t->ok(!file_exists($base . 'thumbs/small_asset1.png'), 'Old small thumbnail does not exist');
$t->ok(!file_exists($base . 'thumbs/medium_asset1.png'), 'Old medium thumbnail does not exist');

$t->info('Move asset');

$a->setFolder($folder);
$a->save();
$a->refresh();

$t->is($a->getPath(), 'test_root/test/renamed.png', '->getPath()');
$t->ok(file_exists($base . 'test/renamed.png'), 'Moved file exists');
$t->ok(file_exists($base . 'test/thumbs/small_renamed.png'), 'Small thumbnail is moved');
$t->ok(file_exists($base . 'test/thumbs/medium_renamed.png'), 'Medium thumbnail is moved');
$t->ok(!file_exists($base . 'renamed.png'), 'File does not exist in old path');
$t->ok(!file_exists($base . 'thumbs/small_renamed.png'), 'Small thumbnail does not exist in old path');
$t->ok(!file_exists($base . 'thumbs/medium_renamed.png'), 'Medium thumbnail does not exist in old path');

$t->info('Move *and* rename asset');

$a->setFilename('asset.png');
$a->setFolder($root);
$a->save();
$a->refresh();

$t->is($a->getPath(), 'test_root/asset.png', '->getPath()');
$t->ok(file_exists($base . 'asset.png'), 'Moved/renamed file exists');
$t->ok(file_exists($base . 'thumbs/small_asset.png'), 'Small thumbnail is moved/renamed');
$t->ok(file_exists($base . 'thumbs/medium_asset.png'), 'Medium thumbnail is moved/renamed');
$t->ok(!file_exists($base . 'test/renamed.png'), 'File does not exist in old path');
$t->ok(!file_exists($base . 'test/thumbs/small_renamed.png'), 'Small thumbnail does not exist in old path');
$t->ok(!file_exists($base . 'test/thumbs/medium_renamed.png'), 'Medium thumbnail does not exist in old path');

$t->info('Delete asset');

$a->delete();
$t->ok(!file_exists($base . 'asset.png'), 'deleted file does not exist');
$t->ok(!file_exists($base . 'thumbs/small_asset.png'), 'Deleted small thumbnail does not exist');
$t->ok(!file_exists($base . 'thumbs/medium_asset.png'), 'Deleted medium thumbnail does not exist');

