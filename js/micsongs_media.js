jQuery(function($) {
  $(document).ready(function(){
  	$('#micsong_upload_button').click(open_media_window);
    $(".select2").select2({ width: '100%' });
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

function micsongsMc() {
  var cat = document.getElementById("mc").value;
  document.getElementById("shortcode3").innerHTML = "[mc categoria=&quot;" + cat + "&quot;]";
}