window.onload = function()
{
  FileBrowserDialogue.submit =  function (URL) {
    window.opener.SetUrl(URL) ;
  }
  FileBrowserDialogue.init();
}
