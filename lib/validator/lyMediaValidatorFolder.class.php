<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaValidatorFolder folder validator class.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  validator
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
class lyMediaValidatorFolder extends sfValidatorBase
{
  public function configure($options = array(), $messages = array())
  {
    $this->addMessage('folder_exists', 'Can\'t create "%name%". A folder with the same name exists in "%parent%"');
  }
  protected function doClean($values)
  {
    $my_id = 0;
    if($values['parent_id'])
    {
      $parent = Doctrine::getTable('lyMediaFolder')
        ->find($values['parent_id']);
    }
    else
    {
      $my_id = $values['id'];
      $object = Doctrine::getTable('lyMediaFolder')
        ->find($my_id);
      $parent = $object->getNode()->getParent();
    }

    if(!$parent || !$parent->getNode()->isValidNode())
    {
      throw new sfValidatorError($this, 'invalid');
    }
    if($children = $parent->getNode()->getChildren())
    {
      foreach($children as $c)
      {
        if($values['name'] == $c->getName() && $c->getId() != $my_id)
        {
          throw new sfValidatorError($this, 'folder_exists', array('name' => $values['name'], 'parent' => $parent->getName()));
        }
      }
    }
    return $values;
  }
}