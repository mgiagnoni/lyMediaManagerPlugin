<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaValidatorFilename filename validator class.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  validator
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
class lyMediaValidatorFilename extends sfValidatorBase
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addOption('allowed_extensions', null);
    $this->addMessage('invalid_extension', 'File extension is not allowed.');
  }
  protected function doClean($value)
  {
    if($this->hasOption('allowed_extensions'))
    {
      $extensions = $this->getOption('allowed_extensions');
      if(!is_array($extensions))
      {
        $extensions = array($extensions);
      }
      $ext = pathinfo($value, PATHINFO_EXTENSION);
      if(!in_array($ext, $extensions))
      {
        throw new sfValidatorError($this, 'invalid_extension');
      }
    }
    return $value;
  }
}