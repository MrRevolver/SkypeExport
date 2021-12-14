<!doctype html>
<head>
   <title>Экспорт сообщений Skype</title>
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
   <style>
      .message-label {
         font-size: 12px !important;
      }
      .message {
         max-width: 85%;
         font-size: 14px;
      }
      .nav-link {
         color: #212529;
      }
      .nav-link.active {
         background-color: #e3f2fd !important;
         color: #212529 !important;
      }
      .nav-link:focus, .nav-link:hover {
         background-color: #f2f6f9;
         color: #212529;
      }
   </style>
</head>
<body>
   <div class="container-fluid">
      <h3>Экспорт сообщений Skype</h3>
      <!--<p style = "white-space:pre-wrap">
      Необходимо выполнить экспорт сообщений скайпа, для этого необходимо:

      1.	В скайпе перейти в настройки (нажать ... и выбрать пункт Настройки).
      2.	В разделе Учетная запись и профиль нажать на ссылку Ваш профиль. Откроется ваш профиль скайпа в браузере.
      3.	В этом же браузере в отдельной вкладке перейти по ссылке https://secure.skype.com/ru/data-export.
      4.	Выбрать пункт Беседы и нажать на кнопку Отправить запрос.
      5.	После этого страницу можно обновить с смотреть статус выполнения запроса.
      6.	Как только запрос на выгрузку будет обработан, нужно скачать файл экспорта и

      </p>-->

      <form enctype="multipart/form-data" action="" method="post">
         <input type="hidden" name="MAX_FILE_SIZE" value="100000000000000000" />
         <input type="file"   name="SkypeFile"/>
         <input type="submit" value="Загрузить сообщения Skype"/>
      </form>

<?
ini_set( 'upload_max_filesize', '100000000000000000');
ini_set ('display_errors', '1');
error_reporting (E_ALL);
ini_set ('memory_limit', '200M');

include_once ('SkypeExport.php');

if (isset($_FILES['SkypeFile'])) {

   if (true) {                                                                   // Если загрузка из переданного файла

      echo "Файл: <b>".$_FILES['SkypeFile']['name']."</b><br>";
      echo "Размер: <b>".$_FILES['SkypeFile']['size']."</b><br>";

      if ($_FILES['SkypeFile']['error'] == 0) echo 'Загружен успешно<br>';
      else                                    die ("<font color=red>Ошибка загрузки. Код ошибки:".$_FILES['SkypeFile']['error']."</font><br>");

      $File = $_FILES['SkypeFile']['tmp_name'];

   } else $File = 'messages.json';

   if (substr ($_FILES['SkypeFile']['name'], -4) == '.tar') {

      rename ($File, $File.'.tar');
      $TarFile = $File.'.tar';
      mkdir ($File, 0700);
      try {
         ob_start();
         passthru ('tar -xvf '.$TarFile.' -C '.$File);
         $UnTar = ob_get_contents();
         ob_end_clean();
      }
      catch (Exception $e) {
         echo 'Путь до папки распаковки: '.$File.'<br>';
         echo 'Содержимое папки после распаковки: <pre>'.print_r (scandir ($File), true).'<pre><br>';
         echo 'Размер файла после распаковки: '.filesize($File).'<br>';
         die ('Ошибка распаковки: '.$e->getMessage().'<br>');
      }

      $FileDir = $File;
      $File = $File.'/messages.json';
      if ($UnTar = 'messages.json') echo 'Распакован успешно<br>';
      else                          echo 'Ошибка распаковки<br>';
   }
   elseif (substr ($_FILES['SkypeFile']['name'], -5) != '.json') die ('<b><font color="red">Ошибка: можно загружать только файлы с расширением ".json" и ".tar".</font></b><br>');

   if (file_exists ($File)) {

      $Skype = new Skype ();
      $Skype->Process (file_get_contents ($File));
      unlink ($File);

   } else echo '<b><font color="red">Ошибка: Не удалось получить содержимое файла выгрузки.</font></b><br>';

   if (isset ($TarFile) and is_file ($TarFile)) unlink ($TarFile);
   if (isset ($FileDir) and is_dir  ($FileDir)) rmdir ($FileDir);
}
?>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>