function lymedia_tiny_init()
{
  /* Edit URL passed to init() depending by name / location of application front controller */
  lyMediaManager.init('/backend.php/ly_media_asset/icons?popup=1');

  tinyMCE.init({
    convert_urls : false,
    mode: "textareas",
    editor_selector : "rich",
    theme: "advanced",
    theme_advanced_toolbar_location: "top",
    theme_advanced_toolbar_align: "left",
    theme_advanced_statusbar_location: "bottom",
    theme_advanced_resizing: true,
    file_browser_callback : "lyMediaManager.fileBrowserCallBack"
  });
}

window.onload=function()
{
 lymedia_tiny_init();
}