<html>
 <head>
 <meta charset="UTF-8">
  <title>
  YTDL WebUI
  </title>
 </head>
 <body>
  <?php
  //pobiera nazwę katalogu roboczego z żądanie GET i usuwa znaki końca linii
  //$workdir = preg_replace('/[\r\n]/','', $_GET['workdir']);
  //usuwa rekurencyjnie kaktalog roboczy
  //shell_exec("rm -R ".$workdir);
  shell_exec("rm -R ".preg_replace('/[\r\n]/','', $_GET['workdir']));
  //Sprawdza czy katalog roboczy został usunięty
  if (!(file_exists($workdir))) {
    echo "Plik został usuniety. Dziękuje za skorzystanie";
  } else {
	echo "Problemy z usuwaniem pliku. Skontaktuj się z administratorem lub radź se sam :-)";
  }
  ?>
 </body>
</html>
