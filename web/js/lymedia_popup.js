var FileBrowserDialogue = {
    curImg: null
    ,
    init : function () {
        var tags = document.getElementById('lymedia_icons').getElementsByTagName('div');
        var els, el;
        for(var i=0; i < tags.length; ++i)
        {
          el = tags[i];
          if(el.className.indexOf('lymedia_asset_frame') != -1)
          {
            el.onclick = function(event) {
              var mn = document.getElementById('lymedia_thumb_menu');
              mn.style.left = event.clientX + 'px';
              mn.style.top = event.clientY + 'px';
              mn.style.visibility = 'visible';

              FileBrowserDialogue.curImg = this;
            }
          }
        }
        els = document.getElementById('lymedia_thumb_menu').getElementsByTagName('span');

        for(i=0; i < els.length; ++i)
        {
          el = els[i];
          el.onclick = function() {
            FileBrowserDialogue.select(this.className);
          }
        }
    },
    select: function (type) {
      var url;
      var els = FileBrowserDialogue.curImg.getElementsByTagName('span');
      if(type == '_original')
      {
        url = els[0].innerHTML;
      }
      else if(type == '_cancel')
      {
        document.getElementById('lymedia_thumb_menu').style.visibility = 'hidden';
        return;
      }
      else
      {
        for(i=1; i < els.length; ++i)
        {
          if(els[i].className == type)
          {
            url = els[i].innerHTML;
          }
        }
      }
      FileBrowserDialogue.submit(url.replace(/^\s+|\s+$/g, ''));
      window.close();
    }
}