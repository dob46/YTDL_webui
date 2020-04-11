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
  function parse_ytdl_json($link,$workdir,$videocodec,$audiocodec){
    $ytdl_json = shell_exec("/usr/bin/youtube-dl -j ".$link);
    $decoded_ytdl_json=json_decode($ytdl_json);
    $format_table_size=count($decoded_ytdl_json->formats);
    $format_table_size--;
    if(!(empty($videocodec))){
        $video_size=get_codec_filesize($videocodec,$format_table_size,$decoded_ytdl_json);
        shell_exec("echo ".$video_size." > ".$workdir."/video_size");
    }
    if(!(empty($audiocodec))){
        $audio_size=get_codec_filesize($audiocodec,$format_table_size,$decoded_ytdl_json);
        shell_exec("echo ".$audio_size." > ".$workdir."/audio_size");
    }
  }
  function find_ytdl_process($workdir){
	 exec("ps aux | grep -i ".$workdir." | grep -v grep", $pids);
     if(empty($pids)) {
        return 0;
     } else {
        return 1;
     }
  }
  function searchForFile($fileToSearchFor){
     $numberOfFiles = count(glob($fileToSearchFor));
     if($numberOfFiles == 0){ return(FALSE); } else { return(TRUE);}
  }
  //W zależności od podanych przez użytkownika kodeków dostosowywuje formaty przekazywane do youtube-dl
  if (empty($_GET['videocodec'])){
	  $jakosc = $_GET['audiocodec'];
  } elseif (empty($_GET['audiocodec'])){
	  $jakosc = $_GET['videocodec'];
  } else {
	  $jakosc = $_GET['videocodec']."+".$_GET['audiocodec'];
  }
  $videocodec=$_GET['videocodec'];
  $audiocodec=$_GET['audiocodec'];
  //Pobiera nazwę katalogu roboczego z żądania GET
  $workdir = $_GET['workdir'];
  //Pobiera link do youtube z żądania GET
  $link = $_GET['link'];
  if(!(empty($videocodec))){
    $video_size=shell_exec("/bin/cat ".$workdir."/video_size");
  }
  if(!(empty($audiocodec))){
    $audio_size=shell_exec("/bin/cat ".$workdir."/audio_size");
  }
  if (file_exists($workdir)) {
  } 
  else {
    shell_exec("/bin/mkdir ".$workdir);
    if(!(file_exists($workdir."/*size"))){
        parse_ytdl_json($link,$workdir,$videocodec,$audiocodec);
    }
    if(!(find_ytdl_process("-j ".$link))){
        shell_exec("echo $(date +%s) > ".$workdir."/create_date");
        //pobiera plik za pomocą youtube-dl
        shell_exec("LC_ALL=pl_PL.UTF-8 /usr/bin/youtube-dl -o \"".$workdir."/%(title)s.%(ext)s\" -f ".$jakosc." ".$link." > /dev/null 2>&1 &");
    }
  }
   if(find_ytdl_process($workdir)){
	  echo("<meta http-equiv='refresh' content='1'>");
	  echo("Trwa pobieranie<br>");
  } else {
    //pobiera nazwę pliku za pomocą youtube-dl
    $file = preg_replace('/[\r\n]/','',shell_exec("LC_ALL=pl_PL.UTF-8 /usr/bin/youtube-dl --get-filename -o \"%(title)s.%(ext)s\" -f ".$jakosc." ".$link));
    //sprawdza czy plik istnieje (używane gdy plik nie może być w kontenerze mp4 lub webm i musi być w kontenerze mkv
    if (file_exists($workdir."/".$file)) {
    } else {
	  $file = str_replace("mp4", "mkv", $file);
	  $file = str_replace("webm", "mkv", $file);
    }
    echo("<a href=".$workdir."/".rawurlencode($file)." download>Pobierz plik ".$file."</a><br><br>");
    echo("<form action='usun.php' method='GET'>");
	echo("<input type='hidden' name='workdir' value=".$workdir.">");
	echo("<input type=submit value='Kliknij po pobraniu aby usunąć'/>");
    echo("</form>");
  }  
  if(searchForFile($workdir."/*part"))
  {
        if(empty($audiocodec)){
            $current_video_size=shell_exec("ls -la ".$workdir." | grep part | awk {'print $5'}");
            if(!($video_size == 0))
            {
                echo("Wideo pobrano ".round(($current_video_size/$video_size)*100)."%");
            }
       }
       elseif(empty($videocodec)){
            $current_audio_size=shell_exec("ls -la ".$workdir." | grep part | awk {'print $5'}");
            if(!($audio_size == 0))
            {
                echo("Audio pobrano ".round(($current_audio_size/$audio_size)*100)."%");
            }
       }
    }
    elseif(searchForFile($workdir."/*".$videocodec."*"))
    {
        $current_video_size=shell_exec("ls -la ".$workdir." | grep ".$videocodec." | awk {'print $5'}");
	    if($video_size == 0){
		}
		else
		{
			echo("Wideo pobrano ".round(($current_video_size/$video_size)*100)."%");
		}
    }
    elseif(searchForFile($workdir."/*".$audiocodec."*"))
    {
        $current_video_size=shell_exec("ls -la ".$workdir." | grep ".$audiocodec." | awk {'print $5'}");
        if($audio_size == 0){
		}
		else{
			echo("<br>Audio pobrano ".round(($current_audio_size/$audio_size)*100)."%");
		}
    }
  ?>
 </body>
</html>
