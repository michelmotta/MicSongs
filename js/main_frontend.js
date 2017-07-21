jQuery(document).ready(function($){

  /*
  * Controls the song's download counter
  */
  $('.download-song').click(function(e){
    var songId = $(this).data('songid');
    var songName = $(this).data('songname');
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


  /*
  * Controls the song's playlist player
  */
  var player;
  var playlist;
  var tracks;
  var current;

  function init(){
    current = 0;
    player = $('.player');
    playlist = $('.playlist');


    $.each(player, function( index, value ) {

      tracks = $(playlist[index]).children('li a');
      len = tracks.length - 1;
      player[index].volume = 0.9;

      $(playlist[index]).find('a:not(.block)').click(function(e){
        e.preventDefault();
        link = $(this);
        current = link.parent().index();
        run(link, player[index]);
      });

      player[index].addEventListener('ended',function(e){
        current++;
        if(current == len){
            current = 0;
            link = $(playlist[index]).find('a:not(.block)')[0];
        }else{
            link = $(playlist[index]).find('a:not(.block)')[current];    
        }
        run($(link),player[index]);
      });
      
    });
  }

  function run(link, player){
    player.src = link.attr('href');
    par = link.parent();
    par.addClass('active').siblings().removeClass('active');
    player.load();
    player.play();
  }
  init();
});