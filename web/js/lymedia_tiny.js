var lyMediaManager_Tiny = function(){};

lyMediaManager_Tiny.prototype = {

  init : function(url)
  {
    this.url = url;
  },

  fileBrowserCallBack : function (field_name, url, type, win)
  {
    if (!this.url)
    {
      throw new Error('URL of browser popup window needed!');

    }

    var params = type == 'image' ? 'images_only=1&editor=tiny' : 'editor=tiny';
    tinyMCE.activeEditor.windowManager.open({
      file :      this.addParams(this.url, params),
      title:      'Media browser',
      width :     650,
      height :    600,
      inline:     'yes',
      resizable : 'yes',
      scrollbars: 'yes'
    },
    {
      input:      field_name,
      type:       type,
      window:     win
    });

    return false;
  },

  addParams: function (url, params)
  {
    return url.indexOf('?') > 0 ? url + '&' + params : url + '?' + params;
  }
}

var lyMediaManager = new lyMediaManager_Tiny();


