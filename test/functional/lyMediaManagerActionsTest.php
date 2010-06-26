<?php

include dirname(__FILE__).'/../bootstrap/functional.php';

$subf1 = Doctrine::getTable('lyMediaFolder')
  ->findOneByName('testsub1');

$browser = new sfTestFunctional(new sfBrowser());
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
  click('li.sf_admin_action_save input', array('ly_media_folder' => array(
    'name' => 'testsub3'
  ) ))->

  with('request')->begin()->
    isParameter('module', 'lyMediaFolder')->
    isParameter('action', 'create')->
  end()->

  with('form')->
    hasErrors(false)->

  with('response')->
    isRedirected()->

  followRedirect()->
  with('request')->begin()->
    isParameter('module', 'lyMediaFolder')->
    isParameter('action', 'edit')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkForm('lyMediaFolderForm')->
  end()->

  info('  2.5 - Check created folder')->
  with('doctrine')->begin()->
    check('lyMediaFolder', array(
      'name' => 'testsub3',
      'relative_path' => 'media/testsub3/',
      'level' => 1
    ))->
  end()->

  info('3 - Edit folder name and parent')->
  click('li.sf_admin_action_save input', array('ly_media_folder' => array(
    'name' => 'testsub1-1',
    'parent_id' => $subf1->getId()
  ) ))->

  with('request')->begin()->
    isParameter('module', 'lyMediaFolder')->
    isParameter('action', 'update')->
  end()->

  with('form')->
    hasErrors(false)->
  
  with('response')->
    isRedirected()->

  followRedirect()->

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
  click('li.sf_admin_action_save input', array('ly_media_asset' => array(
    'folder_id' => $subf1->getId(),
    'title' => 'test',
    'filename' => dirname(__FILE__) . '/../data/assets/asset1.png'
  )))->

  with('request')->begin()->
    isParameter('module', 'lyMediaAsset')->
    isParameter('action', 'create')->
  end()->
  
  with('form')->
    hasErrors(false)->

  with('response')->
    isRedirected()->

  followRedirect()->

  with('request')->begin()->
    isParameter('module', 'lyMediaAsset')->
    isParameter('action', 'edit')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkForm('lyMediaAssetForm')->
  end()->

  info('  4.3 - Check created asset')->
  with('doctrine')->begin()->
    check('lyMediaAsset', array(
      'filename' => 'asset1.png',
      'title' => 'test',
      'type' => 'image/png',
      'folder_id' => $subf1->getId()
    ))->
  end()->
  
  info('5 - Delete created asset')->
  click('li.sf_admin_action_delete a', array(), array(
    'method' => 'delete',
    '_with_csrf' => true))->

  with('request')->begin()->
    isParameter('module', 'lyMediaAsset')->
    isParameter('action', 'delete')->
  end()->

  with('response')->
    isRedirected()->

  followRedirect()
;
