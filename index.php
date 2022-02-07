<?
ini_set('upload_max_filesize', '100000000000000000');
spl_autoload_register('autoloader');

function autoloader($classname)
{
   include_once 'helpers/' . $classname . '.php';
}

include_once 'controller/SkypeController.php';
include_once 'view/SkypeView.php';
include_once 'model/SkypeModel.php';

$Debug = new Debug();
$Controller = new SkypeController();

session_start();

if (isset($_GET['IsExport'])) {

   $InputJson = file_get_contents('php://input');

   if (isset($InputJson)) {

      $Input = json_decode($InputJson);
      $Controller->ProcessExport($Input->select, $Input->format);
   }
} else {

   echo '<!doctype html>
         <head>
            <title>Экспорт сообщений Skype</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
            <style>
               @media (min-width: 576px) {
                  .message-list {
                     width: 75%;
                  }
               }
               @media (min-width: 1400px) {
                  .message-list {
                     width: 83%;
                  }
               }
               .scroll-list {
                  height: 90%;
                  overflow-y: scroll;
                  position: fixed;
                  margin-right: 0px;
               }
               .message-label {
                  font-size: 12px !important;
               }
               .message {
                  max-width: 85%;
                  font-size: 14px;
               }
               .nav-link {
                  color: #212529;
                  padding: 0.5rem;
                  max-width: 250px;
               }
               .nav-link.active {
                  background-color: #e3f2fd !important;
                  color: #212529 !important;
               }
               .nav-link:focus, .nav-link:hover {
                  background-color: #f2f6f9;
                  color: #212529;
               }
               .form-check-input {
                  margin: 0.8rem;
               }
               body {
                  scrollbar-width: thin;  /* толщина */
               }
               ::-webkit-scrollbar {
                  width: 5px;
                  background-color: #dee2e6;
               }
               ::-webkit-scrollbar-thumb {
                  background-color: #555;
               }
            </style>
         </head>
         <body>
            <div class="container-fluid">
               <h3 class="text-center py-3 bg-light">Экспорт сообщений Skype</h3>';

   if (!isset($_REQUEST['Load'])) {

      echo
      '   <form enctype="multipart/form-data" action="" method="post" style="text-align: center; margin: 100px 0 30px 0;">
                  <input type="hidden" name="MAX_FILE_SIZE" value="100000000000000000" />
                  <input type="file"   name="SkypeFile"/>
                  <input type="hidden" name="Load" value=""/>
                  <input type="submit" value="Загрузить сообщения Skype"/>
               </form>
               <p class="text-center">
                  <a href="/instruction.php" target="blank" class="m-3">Инструкция</a>
                  <a href="/conditions.php" target="blank" class="m-3">Условия использования</a>
               </p>';
   } elseif (isset($_FILES['SkypeFile'])) {

      if (!$_FILES['SkypeFile']['error'] == 0) die("<br><font color=red>Ошибка загрузки. Код ошибки:" . $_FILES['SkypeFile']['error'] . "</font>");

      $File = $_FILES['SkypeFile']['tmp_name'];

      if (substr($_FILES['SkypeFile']['name'], -4) == '.tar') {

         rename($File, $File . '.tar');
         $TarFile = $File . '.tar';
         mkdir($File, 0700);

         try {
            ob_start();
            passthru('tar -xvf ' . $TarFile . ' -C ' . $File);
            $UnTar = ob_get_contents();
            ob_end_clean();
         } catch (Exception $e) {
            echo 'Путь до папки распаковки: ' . $File . '<br>';
            echo 'Содержимое папки после распаковки: <pre>' . print_r(scandir($File), true) . '<pre><br>';
            echo 'Размер файла после распаковки: ' . filesize($File) . '<br>';
            die('Ошибка распаковки: ' . $e->getMessage() . '<br>');
         }

         $FileDir = $File;
         $File = $File . '/messages.json';
      } elseif (substr($_FILES['SkypeFile']['name'], -5) != '.json') die('<b><font color="red">Ошибка: можно загружать только файлы с расширением ".json" и ".tar".</font></b><br>');

      if (file_exists($File)) {

         $Controller->Process(file_get_contents($File));
         unlink($File);
      } else echo '<b><font color="red">Ошибка: Не удалось получить содержимое файла выгрузки.</font></b><br>';

      if (isset($TarFile) && is_file($TarFile)) unlink($TarFile);
      if (isset($FileDir) && is_dir($FileDir))  rmdir($FileDir);

      echo  '  </div>' .
         '  <div class="fixed-bottom d-grid gap-2 d-md-flex justify-content-start p-3 bg-light">' .
         '     <div class="d-flex flex-grow-1">' .
         '        <input class="form-check-input" type="checkbox" value="" id="CheckAll" style="margin: 0.6rem 0 0.6rem -3px;" onclick="CheckAll ();"/>' .
         '        <p style="margin: 0.4rem 0 0 1.2rem;">Все</p>' .
         '     </div>' .
         '     <div class="d-flex">' .
         '        <p class="px-3 mb-0 mt-2">Выберите формат: </p>' .
         '        <select id="ExportFormat">' .
         '           <option value="json">JSON</option>' .
         '           <option value="txt">TXT</option>' .
         '           <option value="csv">CSV</option>' .
         '        </select>' .
         '     </div>' .
         '     <div class="d-flex">' .
         '        <p class="px-3 mb-0 mt-2">Выбранные: </p>' .
         '        <div class="btn-group">'.
         '           <button type="button" class="btn btn-danger" onclick="DeleteConversation ();">Удалить</button>' .
         '           <button type="button" class="btn btn-primary" onclick="PushConversation ();" id="MainButton">Выгрузить</button>' .
         '        </div>'.
         '     </div>' .
         '  </div>';
?>
      <script>
         function CheckOption(This) {
            mainbutton = document.getElementById('MainButton');
            mainbutton.setAttribute('onclick', 'PushConversation ()');
            mainbutton.innerText = 'Выгрузить';
         }

         function CheckAll() {
            checkboxes = document.querySelectorAll('.form-check-input');
            if (document.getElementById('CheckAll').checked) checkboxes.forEach(checkbox => checkbox.checked = true);
            else checkboxes.forEach(checkbox => checkbox.checked = false);
         }

         function ResetActiveNavLink(This) {
            document.querySelectorAll('.nav-link').forEach(nav => nav.classList.remove('active'));
            This.classList.add('active');
         }

         function DeleteConversation() {
            document.querySelectorAll('input:checked').forEach(nav => nav.closest('.d-flex').remove());
         }

         function PushConversation() {
            let conversations = {
               'format': '',
               'select': []
            };
            conversations.format = document.getElementById('ExportFormat').value;
            document.querySelectorAll('input:checked').forEach(nav => conversations.select.push(nav.closest('.d-flex').id));
            conversations = JSON.stringify(conversations);
            console.log(conversations);
            postJSON(conversations);
         }

         async function postJSON(conversations) {
            try {
               const response = await fetch('https://www.wentor.ru/Skype/?IsExport', {
                     method: 'POST',
                     body: conversations,
                     headers: {
                        'Content-Type': 'application/json'
                     }
                  })
                  .then(pivot => {
                     mainbutton = document.getElementById('MainButton');
                     mainbutton.setAttribute('onclick', 'FetchDownload ()');
                     mainbutton.innerText = 'Скачать';
                  });
            } catch (error) {
               console.error('Ошибка:', error);
            };
         }

         const FetchDownload = () => {
            fetch("https://www.wentor.ru/Skype/download/file.txt")
               .then(resp => resp.blob())
               .then(blob => {

                  const url = window.URL.createObjectURL(blob);

                  const a = document.createElement("a");
                  a.style.display = "none";
                  a.href = url;
                  a.download = "file.txt";

                  document.body.appendChild(a);
                  a.click();
                  window.URL.revokeObjectURL(url);
               })
               .catch((error) => console.error('Ошибка:', error));
         };
      </script>
<?
      echo '   <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
               <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

            </body>';
   }
}
?>