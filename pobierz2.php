<html>
 <head>
 <meta charset="UTF-8">
  <title>
  YTDL WebUI
  </title>
 </head>
 <body>
  <?php
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
  $video_size=$_GET['video_size'];
  $audio_size=$_GET['audio_size'];
  //Pobiera nazwę katalogu roboczego z żądania GET
  $workdir = $_GET['workdir'];
  //Pobiera link do youtube z żądania GET
  $link = $_GET['link'];
  if (file_exists($workdir)) {
  } 
  else {
    shell_exec("/bin/mkdir ".$workdir);
    //zapisuje datę utworzenia
    shell_exec("echo $(date +%s) > ".$workdir."/create_date");
    //pobiera plik za pomocą youtube-dl
    shell_exec("LC_ALL=pl_PL.UTF-8 /usr/bin/youtube-dl -o \"".$workdir."/%(title)s.%(ext)s\" -f ".$jakosc." ".$link." > /dev/null 2>&1 &");

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
  if(searchForFile($workdir."/*".$videocodec."*"))
  {
	    $current_video_size=shell_exec("ls -la ".$workdir." | grep ".$videocodec." | awk {'print $5'}");
	    if($video_size == 0){
			echo "Trwa pobieranie wideo";
		}
		else{
			echo("Wideo pobrano ".round(($current_video_size/$video_size)*100)."%");
		}
  }
    if(searchForFile($workdir."/*".$audiocodec."*"))
  {
	    $current_audio_size=shell_exec("ls -la ".$workdir." | grep ".$audiocodec." | awk {'print $5'}");
        if($audio_size == 0){
			echo "<br>Trwa pobieranie audio";
		}
		else{
			echo("Audio pobrano ".round(($current_audio_size/$audio_size)*100)."%");
		}
  }
  ?>
 </body>
</html>
