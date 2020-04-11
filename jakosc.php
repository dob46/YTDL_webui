<html>
 <head>
 <meta charset="UTF-8">
  <title>
  YTDL WebUI
  </title>
 </head>
 <body>
  <?php
  function get_codec_filesize($codec,$format_table_size,$decoded_ytdl_json)
	{
	 $index=-1;
	 $index_codec=0;
	 while($codec != $index_codec) {
	  $index++;
	  if($index > $format_table_size)
	  {
		echo "BUG in get_codec_filesize";
		break;
	  }
	  $index_codec=$decoded_ytdl_json->formats[$index]->format_id;
	}
	if($decoded_ytdl_json->formats[$index]->filesize == NULL){
		return 0;
    }
    else{
		return $decoded_ytdl_json->formats[$index]->filesize;
    }
  }
  $link = $_GET['link'];
  $workdir = $_GET['workdir'];
  $videocodec = $_GET['videocodec'];
  $audiocodec = $_GET['audiocodec'];
  $ytdl_json = shell_exec("/usr/bin/youtube-dl -j ".$link);
  $decoded_ytdl_json=json_decode($ytdl_json);
  $format_table_size=count($decoded_ytdl_json->formats);
  $format_table_size--; 
  $video_size=get_codec_filesize($videocodec,$format_table_size,$decoded_ytdl_json);
  $audio_size=get_codec_filesize($audiocodec,$format_table_size,$decoded_ytdl_json);
  ?>
  <!--umożliwia pobranie pliku i przekazuje dalej zmienną workdir (nazwę katalogu roboczego )-->
    <br>Ustalono rozmiary plików, kliknij Dalej, aby kontynuować
    <form action="pobierz2.php" method="GET">
	  <input type="hidden" name="link" value="<?php echo $link; ?>">
	  <input type="hidden" name="workdir" value="<?php echo $workdir; ?>">
	  <input type="hidden" name="videocodec" value="<?php echo $videocodec; ?>">
	  <input type="hidden" name="audiocodec" value="<?php echo $audiocodec; ?>">
	  <input type="hidden" name="video_size" value="<?php echo $video_size; ?>">
	  <input type="hidden" name="audio_size" value="<?php echo $audio_size; ?>">
	  <input type=submit value="Dalej"/>
  </form>
 </body>
</html>
