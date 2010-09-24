<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Functional tests for file system errors.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  functional tests
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
include dirname(__FILE__).'/../bootstrap/functional.php';
Doctrine::loadData(dirname(__FILE__) . '/../data/fixtures/fixtures_fs.yml');

$root = Doctrine::getTable('lyMediaFolder')
  ->findOneByName('media');

$subf1 = Doctrine::getTable('lyMediaFolder')
  ->findOneByName('testsub1');

$subf2 = Doctrine::getTable('lyMediaFolder')
  ->findOneByName('testsub2');

//Creates some test assets. Cannot be done in fixtures
$file = dirname(__FILE__) . '/../data/assets/asset1.png';
$asset = new lyMediaAsset();
$asset->setFolder($subf1);
$asset->setFilename($file);
$asset->save();
$asset->refresh();

$file = dirname(__FILE__) . '/../data/assets/asseta.png';
$asset2 = new lyMediaAsset();
$asset2->setFolder($subf2);
$asset2->setFilename($file);
$asset2->save();
$asset2->refresh();

$base = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR;
chmod($base . 'testsub1', 0555);

$browser = new lyMediaTestFunctional(new sfBrowser());
$browser->setTester('doctrine', 'sfTesterDoctrine');

$browser->
  info('1 - Unwritable destination folder')->
  info('  1.1 - Create folder')->
  get('/ly_media_asset')->
  click('li.sf_admin_action_new_folder a')->

  click('li.sf_admin_action_save input', array('ly_media_folder' => array(
    'parent_id' => $subf1->getId(),
    'name' => 'test'
  )))->

  with('form')->begin()->
    hasErrors(1)->
    hasGlobalError('parent_unwritable')->
  end()->

  info('  1.2 - Move folder')->
  get('/ly_media_folder/' . $subf2->getId() . '/edit')->

  click('li.sf_admin_action_save input', array('ly_media_folder' => array(
    'parent_id' => $subf1->getId(),
  )))->

  with('form')->begin()->
    hasErrors(1)->
    hasGlobalError('parent_unwritable')->
  end()->

  info('  1.3 - Upload asset')->
  get('/ly_media_asset')->
  click('li.sf_admin_action_new a')->
  click('li.sf_admin_action_save input', array('ly_media_asset' => array(
    'folder_id' => $subf1->getId(),
    'title' => 'test',
    'filename' => dirname(__FILE__) . '/../data/assets/asset1.png'
  )))->

  with('form')->begin()->
    hasErrors(1)->
    hasGlobalError('folder_unwritable')->
  end()->
  
  info('  1.4 - Rename asset')->
  get('/ly_media_asset/' . $asset->getId() . '/edit')->
  click('li.sf_admin_action_save input', array(
    'filename' => 'renamed'
  ))->

  with('form')->begin()->
    hasErrors(1)->
    hasGlobalError('folder_unwritable')->
  end()->

  info('  1.5 - Delete asset')->
  get('/ly_media_asset')->
  click('li.sf_admin_action_delete a[href$=ly_media_asset/'. $asset->getId() . ']', array(), array('method' => 'delete', '_with_csrf' => true))->
  
  with('response')->
    isRedirected()->

  followRedirect()->

  with('response')->
  checkElement('div.error:contains("asset1.png")')->
  
  info('  1.6 - Move asset')->
  get('/ly_media_asset/' . $asset2->getId() . '/edit')->
  click('li.sf_admin_action_save input', array('ly_media_asset' => array(
    'folder_id' => $subf1->getId()
  )))->

  with('form')->begin()->
    hasErrors(1)->
    hasGlobalError('folder_unwritable')->
  end()
;

//Make writable to allow removal
chmod($base . 'testsub1', 0755);
