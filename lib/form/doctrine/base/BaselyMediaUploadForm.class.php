<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Upload form base class.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  form
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */

class BaselyMediaUploadForm extends BaselyMediaAssetForm
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
    
    $this->widgetSchema['folder_id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['folder_id'] = new sfValidatorInteger();

    $this->widgetSchema['filename'] = new sfWidgetFormInputFile();
    $this->validatorSchema['filename'] = new lyMediaValidatorFile(array(
      'required' => 'true',
      'path' => $this->getOption('folder')->getRelativePath(),
      'mime_types' => lyMediaTools::getAllowedMimeTypes(),
      'allowed_extensions' => lyMediaTools::getAllowedExtensions()
    ));
    $this->widgetSchema['filename']->setLabel('Upload file');
    $this->validatorSchema->setPostValidator(new lyMediaValidatorAsset());
  }

  public function processValues($values)
  {
    $values['type'] = $values['filename']->getType();
    $values['filesize'] = round($values['filename']->getSize() / 1024);
    return parent::processValues($values);
  }
  protected function doBind(array $values)
  {
    $values['folder_id'] = $this->getOption('folder')->getId();
    parent::doBind($values);
  }
}