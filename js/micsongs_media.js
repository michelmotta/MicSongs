jQuery(function($) {
  $(document).ready(function(){
  	$('#micsong_upload_button').click(open_media_window);
  });

  function open_media_window() {
  	if (this.window === undefined) {
     	this.window = wp.media({
        title: 'Inserir Música',
        library: {
        	type: 'audio/mpeg'
        },
        multiple: false,
        button: {
          text: 'Inserir'
        }
      });

    	var self = this; // Needed to retrieve our variable in the anonymous function below
      this.window.on('select', function() {
        var first = self.window.state().get('selection').first().toJSON();
        console.log(first.url);
        $('#song-url').val(first.url);
        //wp.media.editor.insert('[myshortcode id="' + first.id + '"]');
      });
    }
    this.window.open();
    return false;
  }
});