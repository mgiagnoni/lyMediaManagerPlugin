<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaFolder create form base class.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  form
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */

class BaselyMediaCreateFolderForm extends BaselyMediaFolderForm
{
  public function setup()
  {
    parent::setup();

     unset(
      $this['title'],
      $this['description'],
      $this['relative_path'],
      $this['lft'],
      $this['rgt'],
      $this['level'],
      $this['created_at'],
      $this['updated_at']
    );
    $this->widgetSchema['parent_id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['parent_id'] = new sfValidatorInteger();
    
    $this->widgetSchema['name']->setAttribute('size', 10);
    $this->widgetSchema['name']->setLabel('Add subfolder');
    
    $this->validatorSchema['name'] = new sfValidatorRegex(array(
      'required' => true,
      'must_match' => false,
      'pattern' => '#^' . lyMediaThumbnails::getThumbnailFolder() . '$|[^a-z0-9-_]#i'
    ));

    $this->validatorSchema->setPostValidator(new lyMediaValidatorFolder());
  }
  public function updateObject($values = null)
  {
    $object = parent::updateObject($values);
    $object->setParent($this->getOption('parent'));
    
    return $object;
  }
  protected function doBind(array $values)
  {
    $values['parent_id'] = $this->getOption('parent')->getId();
    parent::doBind($values);
  }
}
