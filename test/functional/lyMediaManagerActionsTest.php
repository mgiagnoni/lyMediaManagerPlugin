<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Functional tests
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  functional tests
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
include dirname(__FILE__).'/../bootstrap/functional.php';

$subf1 = Doctrine::getTable('lyMediaFolder')
  ->findOneByName('testsub1');

$subf2 = Doctrine::getTable('lyMediaFolder')
  ->findOneByName('testsub2');

$browser = new lyMediaTestFunctional(new sfBrowser());
$browser->setTester('doctrine', 'sfTesterDoctrine');

$browser->
  info('1 - Assets list view')->
  get('/ly_media_asset')->

  with('request')->begin()->
    isParameter('module', 'lyMediaAsset')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('td.sf_admin_list_td_td_image img[src$="asset1.png"]')->
    checkElement('td.sf_admin_list_td_td_image img[src$="asset2.png"]')->
    checkElement('td.sf_admin_list_td_td_image img[src$="asset3.png"]')->
  end()->

  info('2 - New folder')->
  click('li.sf_admin_action_new_folder a')->

  with('request')->begin()->
    isParameter('module', 'lyMediaFolder')->
    isParameter('action', 'new')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkForm('lyMediaFolderForm')->
  end()->

  info('  2.1 - Submit empty values')->
  click('li.sf_admin_action_save input')->

  with('request')->begin()->
    isParameter('module', 'lyMediaFolder')->
    isParameter('action', 'create')->
  end()->

  with('form')->begin()->
    hasErrors(1)->
    isError('name', 'required')->
  end()->

  info('  2.2 - Submit invalid values')->
  click('li.sf_admin_action_save input', array('ly_media_folder' => array(
    'name' => 'te/st'
  ) ))->

  with('form')->begin()->
    hasErrors(1)->
    isError('name', 'invalid')->
  end()->

  info('  2.3 - Submit invalid values (folder exists)')->
  click('li.sf_admin_action_save input', array('ly_media_folder' => array(
    'name' => 'testsub1'
  ) ))->
  
  with('form')->begin()->
    hasErrors(1)->
    hasGlobalError('folder_exists')->
  end()->

  info('  2.4 - Submit valid values')->
  saveNew('lyMediaFolder', array('ly_media_folder' => array(
    'name' => 'testsub3'
  )))->

  info('  2.5 - Check created folder')->
  with('doctrine')->begin()->
    check('lyMediaFolder', array(
      'name' => 'testsub3',
      'relative_path' => 'media/testsub3/',
      'level' => 1
    ))->
  end()->

  info('3 - Edit folder name and parent')->
  saveEdit('lyMediaFolder', array('ly_media_folder' => array(
    'name' => 'testsub1-1',
    'parent_id' => $subf1->getId()
  )))->

  info('  3.1 - Check edited folder')->
  with('doctrine')->begin()->
    check('lyMediaFolder', array(
      'name' => 'testsub1-1',
      'relative_path' => 'media/testsub1/testsub1-1/',
      'level' => 2
    ))->
  end()->

  info('  3.2 - Go back')->
  click('li.sf_admin_action_list a')->

  with('request')->begin()->
    isParameter('module', 'lyMediaAsset')->
    isParameter('action', 'index')->
  end()->

  with('response')->
    isStatusCode(200)->

  info('4 - New asset')->
  click('li.sf_admin_action_new a')->

  with('request')->begin()->
    isParameter('module', 'lyMediaAsset')->
    isParameter('action', 'new')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkForm('lyMediaAssetForm')->
  end()->

  info('  4.1 - Submit empty values')->
  click('li.sf_admin_action_save input')->

  with('request')->begin()->
    isParameter('module', 'lyMediaAsset')->
    isParameter('action', 'create')->
  end()->

  with('form')->begin()->
    hasErrors(1)->
    isError('filename', 'required')->
  end()->

  info('  4.2 - Submit valid values')->
  saveNew('lyMediaAsset', array('ly_media_asset' => array(
    'folder_id' => $subf1->getId(),
    'title' => 'test',
    'filename' => dirname(__FILE__) . '/../data/assets/asset1.png'
  )))->

  info('  4.3 - Check new asset')->
  with('doctrine')->begin()->
    check('lyMediaAsset', array(
      'filename' => 'asset1.png',
      'title' => 'test',
      'type' => 'image/png',
      'folder_id' => $subf1->getId()
    ))->
  end()->

  info('  4.4 - Check new asset files')
;

$asset = Doctrine::getTable('lyMediaAsset')
  ->findOneByTitle('test');

$folder = $asset->getFolderPath();
$file = $asset->getFilename();

$browser->isFile($folder, $file)->
  info('5 - Rename asset')->
  saveEdit('lyMediaAsset', array('ly_media_asset' => array(
    'filename' => 'asset1_renamed.png'
  )))->

  info('  5.1 - Check renamed asset')->
  with('doctrine')->begin()->
    check('lyMediaAsset', array(
      'filename' => 'asset1_renamed.png',
      'title' => 'test',
      'type' => 'image/png',
      'folder_id' => $subf1->getId()
    ))->
  end()->

  info('  5.2 - Check renamed asset files')->
  info('    5.2.1 - Old filename must not exist')->
  isntFile($folder, $file)->
  info('    5.2.2 - New filename must exist')->
  isFile($folder, 'asset1_renamed.png')->

  info('6 - Move asset')->
  saveEdit('lyMediaAsset', array('ly_media_asset' => array(
    'folder_id' => $subf2->getId()
  )))->

  info('  6.1 - Check moved asset')->
  with('doctrine')->begin()->
    check('lyMediaAsset', array(
      'filename' => 'asset1_renamed.png',
      'title' => 'test',
      'type' => 'image/png',
      'folder_id' => $subf2->getId()
    ))->
  end()->

  info('  6.2 - Check moved asset files')->
  info('    6.2.1 - File in source folder must not exist')->
  isntFile($folder, 'asset1_renamed.png')->
  info('    6.2.2 - File in destination folder must exist')->
  isFile($subf2->getRelativePath(), 'asset1_renamed.png')->

  info('7 - Icons view')->
  get('/ly_media_asset/icons')->
  with('request')->begin()->
    isParameter('module', 'lyMediaAsset')->
    isParameter('action', 'icons')->
  end()->
  
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('#lymedia_folder_path', '/media/')->
    checkElement('div.lymedia_up', false)->
    checkElement('div.lymedia_folder', 2)->
    checkElement('div.lymedia_folder_frame a img[title="testsub1"]')->
    checkElement('div.lymedia_folder_frame a img[title="testsub2"]')->
    checkElement('div.lymedia_folder_stats span.folders', '/2 folders/')->
    checkElement('div.lymedia_folder_stats span.files', '/51 files/')->
  end()->

  info('8 - Sorting')->

  info('  8.1 - Check default sort (name asc)')->

  with('response')->begin()->
    checkElement('.lymedia_asset .lymedia_caption', '/asset1.png/', array('position' => 0))->
    checkElement('.lymedia_asset .lymedia_caption', '/asset2.png/', array('position' => 1))->
    checkElement('.lymedia_asset .lymedia_caption', '/asset3.png/', array('position' => 2))->
  end()->

  info('  8.2 - Change sort direction')->
  click('a[href$="dir=desc"]')->

  with('request')->begin()->
    isParameter('module', 'lyMediaAsset')->
    isParameter('action', 'icons')->
    isParameter('dir', 'desc')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('.lymedia_asset .lymedia_caption', '/zzasset.png/', array('position' => 0))->
  end()->

  info('  8.3 - Change sort field')->
  click('a[href$="sort=date"]')->

  with('request')->begin()->
    isParameter('module', 'lyMediaAsset')->
    isParameter('action', 'icons')->
    isParameter('sort', 'date')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('.lymedia_asset .lymedia_caption', '/asset5.png/', array('position' => 0))->
  end()->

  info('  8.4 - Change sort direction')->
  click('a[href$="dir=asc"]')->

  with('request')->begin()->
    isParameter('module', 'lyMediaAsset')->
    isParameter('action', 'icons')->
    isParameter('dir', 'asc')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('.lymedia_asset .lymedia_caption', '/asset3.png/', array('position' => 0))->
    checkElement('.lymedia_asset .lymedia_caption', '/asset1.png/', array('position' => 1))->
    checkElement('.lymedia_asset .lymedia_caption', '/zzasset.png/', array('position' => 2))->
    checkElement('.lymedia_asset .lymedia_caption', '/asset2.png/', array('position' => 3))->
  end()->

  info('9 - Paging')->

  with('response')->begin()->
    checkElement('div.lymedia_asset', 20)->
    checkElement('.pagination a.page', 2)->
    checkElement('.pagination a.page', '!/1/')->
    checkElement('.pagination a.page', '/2/', array('position' => 0))->
    checkElement('.pagination a.page', '/3/', array('position' => 1))->
  end()->

  info('  9.1 - Next page')->
  click('.pagination a.page', array(), array('position' => 1))->

  with('request')->begin()->
    isParameter('module', 'lyMediaAsset')->
    isParameter('action', 'icons')->
    isParameter('page', '2')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('div.lymedia_asset', 20)->
    checkElement('.pagination a.page', '/1/',array('position' => 0))->
    checkElement('.pagination a.page', '!/2/')->
    checkElement('.pagination a.page', '/3/', array('position' => 1))->
  end()->

  info('  9.1.1 - Next page')->
  click('.pagination a.page', array(), array('position' => 2))->

  with('request')->begin()->
    isParameter('module', 'lyMediaAsset')->
    isParameter('action', 'icons')->
    isParameter('page', '3')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('div.lymedia_asset', 11)->
    checkElement('.pagination a.page', '/1/', array('position' => 0))->
    checkElement('.pagination a.page', '/2/', array('position' => 1))->
    checkElement('.pagination a.page', '!/3/')->
  end()->

  info('10 - Navigate first subfolder')->
  click('a[href$="ly_media_asset/icons/' . $subf1->getId() . '"]')->

  with('request')->begin()->
    isParameter('module', 'lyMediaAsset')->
    isParameter('action', 'icons')->
    isParameter('folder_id', $subf1->getId())->
  end()
;