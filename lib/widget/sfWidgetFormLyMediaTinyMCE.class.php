<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormLyMediaTinyMCE widget class.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  widget
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
class sfWidgetFormLyMediaTinyMCE extends sfWidgetFormTextarea
{
  protected function configure($options = array(), $attributes = array())
  {
    $this->addOption('theme', 'advanced');
    $this->addOption('width');
    $this->addOption('height');
    $this->addOption('config', '');
    $this->addOption('tiny_mce_js', 'tiny_mce/tiny_mce');
    $this->addOption('file_browser_js', '/lyMediaManagerPlugin/js/lymedia_tiny');
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $textarea = parent::render($name, $value, $attributes, $errors);

    $js = sprintf(<<<EOF
<script type="text/javascript">
  /* <![CDATA[ */
  lyMediaManager.init('%s');
  
  tinyMCE.init({
    convert_urls : false,
    mode: "exact",
    elements: "%s",
    theme: "%s",
    %s
    %s
    theme_advanced_toolbar_location: "top",
    theme_advanced_toolbar_align: "left",
    theme_advanced_statusbar_location: "bottom",
    theme_advanced_resizing: true,
    file_browser_callback : "lyMediaManager.fileBrowserCallBack"
    %s
  });
  /* ]]> */
</script>
EOF
    ,
      url_for('@ly_media_asset_icons?popup=1',true),
      $this->generateId($name),
      $this->getOption('theme'),
      $this->getOption('width')  ? sprintf('width: "%spx",', $this->getOption('width')) : '',
      $this->getOption('height') ? sprintf('height: "%spx",', $this->getOption('height')) : '',
      $this->getOption('config') ? ",\n".$this->getOption('config') : ''
    );

    return $textarea.$js;
  }
  public function getJavascripts()
  {
    $js = array();
    
    if(false !== $this->getOption('tiny_mce_js'))
    {
      $js[] = $this->getOption('tiny_mce_js');
    }
    if(false !== $this->getOption('file_browser_js'))
    {
      $js[] = $this->getOption('file_browser_js');
    }
    return $js;
  }
}
