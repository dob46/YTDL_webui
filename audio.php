<?php
/*
 * video.php
 * 
 * Copyright 2019 Unknown <tomasz@tomek-pc>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 * 
 */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>YTDL WebUI</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 1.34.1" />
</head>

<body>
	<?php
	//Pobiera link do youtube z żądania GET
	$link = $_GET['link'];
	//Pobiera nazwę katalogu roboczego z żądania GET
	$workdir = $_GET['workdir'];
	//Pobiera kodek wideo z żądania GET
	$videocodec = $_GET['videocodec'];
	//Pobiera formaty za pomocą youtube-dl ze zmiennej $link (zawiera w sobie link do yt) i zapisuje każdą linijkę w tabeli $formaty
	exec("/usr/bin/youtube-dl -F ".$link , $formaty );
	//za pomoća funkcji grep wybiera dostępne formaty audio (z dopiskiem audio only)
	$formaty_audio = preg_grep('/(audio only)/', $formaty);
	//Za pomocą pętli foreach wypisuje każdy format audio
	echo "<pre>";
	foreach($formaty_audio as $x => $value) {
		echo $value."\r\n";
	}
	echo "</pre>";
	?>
<!--
	Za pomocą formularza pobiera od użytkownika kodek audio i wysyła go dalej razem ze zmiennymi:
	link (link do yt)
	workdir (nazwa katalogu roboczego)
	videocodec (kodek wideo)
-->
	<form action="jakosc.php" method="GET">
		<br>Wybierz format wideo:
		<br><input type=text name="audiocodec" autofocus="autofocus" /><br/>
		<input type="hidden" name="link" value="<?php echo $link; ?>">
		<input type="hidden" name="workdir" value="<?php echo $workdir; ?>">
		<input type="hidden" name="videocodec" value="<?php echo $videocodec; ?>">
		<input type=submit value="Dalej"/>
	</form>
</body>

</html>
