jQuery(document).ready(function($){
    $('.download-song').click(function(e){ // <- added
      //e.preventDefault(); // <- added to prevent normal form submission
      var songId = $(this).data('songid');
      var songName = $(this).data('songname');
      //alert(songId +" - "+songName)
      $.post(
        WPaAjax.ajaxurl,
        {
          action : 'micsongs_downloads',
          songId : songId,
          songName : songName,
        },
        function(response) {
          $('#resposta').append( response );
        }
      );
      return true;
    });
});