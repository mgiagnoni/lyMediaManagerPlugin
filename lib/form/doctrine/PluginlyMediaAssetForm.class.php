<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaAsset form class.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  form
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
abstract class PluginlyMediaAssetForm extends BaselyMediaAssetForm
{
  public function setup()
  {
    parent::setup();

    unset(
      $this['type'],
      $this['filesize'],
      $this['created_at'],
      $this['updated_at']
    );

    $this->widgetSchema['folder_id']->setOption('method','getIndentName');
    
    if($this->isNew())
    {
      $this->widgetSchema['filename'] = new sfWidgetFormInputFile();
      $this->validatorSchema['filename'] = new lyMediaValidatorFile(array(
        'required' => 'true',
        'path' => $this->getOption('upload_root') . sfConfig::get('app_lyMediaManager_media_root', 'media'),
        'mime_types' => lyMediaTools::getAllowedMimeTypes(),
        'allowed_extensions' => lyMediaTools::getAllowedExtensions()
      ));

      if($user = $this->getOption('user'))
      {
        if($user->getAttribute('view') == 'icons' && $user->getAttribute('folder_id'))
        {
          $this->setDefault('folder_id', $user->getAttribute('folder_id'));
        }
      }
    }
    else
    {
      $query= Doctrine_query::create()
        ->from('lyMediaFolder')
        ->where('id != ?', $this->getObject()->getFolder()->getId());
      $this->widgetSchema['folder_id']->setOption('query', $query);
      $this->widgetSchema['folder_id']->setOption('add_empty', 'Move to ...');

      $this->widgetSchema['filename'] = new sfWidgetFormInput();
      $this->validatorSchema['filename'] = new lyMediaValidatorFilename(array(
        'required' => true,
        'allowed_extensions' => lyMediaTools::getAllowedExtensions()
      ));
    }
    $this->validatorSchema->setPostValidator(new lyMediaValidatorAsset());
  }

  protected function doBind(array $values)
  {
    if($this->isNew())
    {
      if($values['folder_id'])
      {
        $folder = lyMediaFolderTable::getInstance()
          ->find($values['folder_id']);
        $this->validatorSchema['filename']
          ->setOption('path', $this->getOption('upload_root') . $folder->getPath());
      }
    }
    else
    {
      if(empty($values['folder_id']))
      {
        //Folder unchanged
        $values['folder_id'] = $this->getObject()->getFolderId();
      }
    }
    parent::doBind($values);
  }
  
  public function processValues($values)
  {
    if($this->isNew())
    {
      $values['type'] = $values['filename']->getType();
      $values['filesize'] = round($values['filename']->getSize() / 1024);
    }
    return parent::processValues($values);
  }
}
