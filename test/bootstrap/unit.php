<?php
$_test_dir = realpath(dirname(__FILE__).'/..');

// configuration
require_once dirname(__FILE__).'/../../../../config/ProjectConfiguration.class.php';
$configuration = ProjectConfiguration::hasActive() ? ProjectConfiguration::getActive() : new ProjectConfiguration(realpath($_test_dir.'/..'));

// autoloader
$autoload = sfSimpleAutoload::getInstance(sfConfig::get('sf_cache_dir').'/project_autoload.cache');
$autoload->loadConfiguration(sfFinder::type('file')->name('autoload.yml')->in(array(
  sfConfig::get('sf_symfony_lib_dir').'/config/config',
  sfConfig::get('sf_config_dir'),
)));
$autoload->register();

// lime
include $configuration->getSymfonyLibDir().'/vendor/lime/lime.php';

$configuration = ProjectConfiguration::getApplicationConfiguration( 'frontend', 'test', true);

//Remove test dirs
$rootdir = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'test_root';
if(is_dir($rootdir))
{
  $files = sfFinder::type('any')->maxdepth(4)->in($rootdir);
  array_unshift($files, $rootdir);
  $fs = new sfFileSystem();
  $fs->remove($files);
}

new sfDatabaseManager($configuration);
Doctrine_Core::dropDatabases();
Doctrine_Core::createDatabases();
Doctrine_Core::createTablesFromModels(sfConfig::get('sf_lib_dir').'/model');