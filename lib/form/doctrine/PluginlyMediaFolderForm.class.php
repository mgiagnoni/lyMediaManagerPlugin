<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaFolder form class.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  form
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
abstract class PluginlyMediaFolderForm extends BaselyMediaFolderForm
{
  public function setup()
  {
    parent::setup();

    unset(
      $this['relative_path'],
      $this['lft'],
      $this['rgt'],
      $this['level'],
      $this['created_at'],
      $this['updated_at']
    );

    $query = Doctrine_Query::create()
      ->from('lyMediaFolder f');

    if(!$this->isNew())
    {
      $query->where('f.lft < ? OR f.rgt > ?', array(
        $this->getObject()->getLft(),
        $this->getObject()->getRgt()
      ));
    }

    $this->widgetSchema['parent_id'] = new sfWidgetFormDoctrineChoice(array(
      'model' => 'lyMediaManagerFolder',
      'order_by' => array('lft', ''),
      'method' => 'getIndentName',
      'query' => $query
    ));

    if($this->isNew())
    {
      if($user = $this->getOption('user'))
      {
        if($user->getAttribute('view') == 'icons' && $user->getAttribute('folder_id'))
        {
          $this->setDefault('parent_id', $user->getAttribute('folder_id'));
        }
      }
    }
    else
    {
      $this->widgetSchema['parent_id']->setOption('add_empty', 'Move to ...');
    }

    $this->validatorSchema['parent_id'] = new sfValidatorDoctrineChoice(array(
      'required' => $this->isNew(),
      'model' => 'lyMediaFolder'
    ));
    
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

    if($parent_id = $this->getValue('parent_id'))
    {
      $parent = $object->getTable()
        ->find($parent_id);

      $object->setParent($parent);
    }
    return $object;
  }
}
