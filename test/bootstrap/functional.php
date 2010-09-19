<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Functional tests bootstrap
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  functional tests
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
if (!isset($app))
{
  $app = 'frontend';
}

require_once dirname(__FILE__).'/../../../../config/ProjectConfiguration.class.php';
$configuration = ProjectConfiguration::getApplicationConfiguration($app, 'test', isset($debug) ? $debug : true);
sfContext::createInstance($configuration);

//Remove test dirs
$rootdir = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . sfConfig::get('app_lyMediaManager_media_root', 'media');
$test_dirs = array($rootdir . '/testsub1', $rootdir . '/testsub2');
$files = sfFinder::type('any')->maxdepth(4)->in($test_dirs);

$fs = new sfFileSystem();
$fs->remove($files);
$fs->remove(array_filter($test_dirs, 'file_exists'));

new sfDatabaseManager($configuration);
Doctrine::loadData(dirname(__FILE__) . '/../data/fixtures/');

//Copy assets files for test
copy(dirname(__FILE__) . '/../data/assets/asseta.png', dirname(__FILE__) . '/../data/assets/asset1.png');