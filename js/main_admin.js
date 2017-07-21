/*
* Initializes admin scripts and the default wordpress uploader
*/
jQuery(function($) {
  $(document).ready(function(){
  	$('#micsong_upload_button').click(open_media_window);
    $(".select2").select2({ width: '100%' });
    $(".select2-tag").select2({ multiple: true, tags: true, width: '100%' });
    $(".leaderMultiSelctdropdown").select2("val");
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

    	var self = this;
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

/*
* Function for generate sortcode
*/
function micsongsMs() {
  var name = document.getElementById("ms").value;
  document.getElementById("shortcode1").innerHTML = "[mss musica=&quot;" + name + "&quot;]";
}


/*
* Function for generate sortcode
*/
function micsongsMp() {
  var values = jQuery('.select2-tag').select2("val");
  document.getElementById("shortcode2").innerHTML = "[msp musica=&quot;" + values.join(", ") + "&quot;]";
}


/*
* Function for generate sortcode
*/
function micsongsMc() {
  var cat = document.getElementById("mc").value;
  document.getElementById("shortcode3").innerHTML = "[msc categoria=&quot;" + cat + "&quot;]";
}