<?php

if (!isset($app))
{
  $app = 'frontend';
}

require_once dirname(__FILE__).'/../../../../config/ProjectConfiguration.class.php';
$configuration = ProjectConfiguration::getApplicationConfiguration($app, 'test', isset($debug) ? $debug : true);
sfContext::createInstance($configuration);

//Remove test dirs
$rootdir = lyMediaTools::getBasePath();
@rmdir($rootdir . 'media/testsub1/testsub1-1');
@rmdir($rootdir . 'media/testsub1/');
@rmdir($rootdir . 'media/testsub2/');

//Copy assets files for test
copy(dirname(__FILE__) . '/../data/assets/asseta.png', dirname(__FILE__) . '/../data/assets/asset1.png');
copy(dirname(__FILE__) . '/../data/assets/assetb.png', dirname(__FILE__) . '/../data/assets/asset2.png');
copy(dirname(__FILE__) . '/../data/assets/assetc.png', dirname(__FILE__) . '/../data/assets/asset3.png');

new sfDatabaseManager($configuration);
Doctrine::loadData(dirname(__FILE__) . '/../data/fixtures/');