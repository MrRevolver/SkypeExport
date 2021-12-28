<!doctype html>
<head>
   <title>������� ��������� Skype</title>
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
      }
      .nav-link.active {
         background-color: #e3f2fd !important;
         color: #212529 !important;
      }
      .nav-link:focus, .nav-link:hover {
         background-color: #f2f6f9;
         color: #212529;
      }
      body {
         scrollbar-width: thin;  /* ������� */
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

<?
ini_set( 'upload_max_filesize', '100000000000000000');
ini_set ('display_errors', '1');
error_reporting (E_ALL);
ini_set ('memory_limit', '200M');

   echo '<body>
            <div class="container-fluid">
               <h3>������� ��������� Skype</h3>';

If (!isset ($_REQUEST['Load'])) {

   echo '      <!--<p style = "white-space:pre-wrap">
               ���������� ��������� ������� ��������� ������, ��� ����� ����������:

               1.	� ������ ������� � ��������� (������ ... � ������� ����� ���������).
               2.	� ������� ������� ������ � ������� ������ �� ������ ��� �������. ��������� ��� ������� ������ � ��������.
               3.	� ���� �� �������� � ��������� ������� ������� �� ������ https://secure.skype.com/ru/data-export.
               4.	������� ����� ������ � ������ �� ������ ��������� ������.
               5.	����� ����� �������� ����� �������� � �������� ������ ���������� �������.
               6.	��� ������ ������ �� �������� ����� ���������, ����� ������� ���� �������� �

               </p>-->

               <form enctype="multipart/form-data" action="" method="post">
                  <input type="hidden" name="MAX_FILE_SIZE" value="100000000000000000" />
                  <input type="file"   name="SkypeFile"/>
                  <input type="hidden" name="Load" value=""/>
                  <input type="submit" value="��������� ��������� Skype"/>
               </form>';
}
else {

   if (isset($_FILES['SkypeFile'])) {

      if (true) {                                                                   // ���� �������� �� ����������� �����

         echo '���� <b>'.$_FILES['SkypeFile']['name'].'</b>';
         if ($_FILES['SkypeFile']['error'] == 0) echo ' �������� �������<br>';
         else                                    die ("<br><font color=red>������ ��������. ��� ������:".$_FILES['SkypeFile']['error']."</font>");

         echo "������: <b>".$_FILES['SkypeFile']['size']."</b><br>";

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
            echo '���� �� ����� ����������: '.$File.'<br>';
            echo '���������� ����� ����� ����������: <pre>'.print_r (scandir ($File), true).'<pre><br>';
            echo '������ ����� ����� ����������: '.filesize($File).'<br>';
            die ('������ ����������: '.$e->getMessage().'<br>');
         }

         $FileDir = $File;
         $File = $File.'/messages.json';
      }
      elseif (substr ($_FILES['SkypeFile']['name'], -5) != '.json') die ('<b><font color="red">������: ����� ��������� ������ ����� � ����������� ".json" � ".tar".</font></b><br>');

      if (file_exists ($File)) {

         include_once ('SkypeExport.php');
         $Skype = new Skype ();
         $Skype->Process (file_get_contents ($File));
         unlink ($File);

      } else echo '<b><font color="red">������: �� ������� �������� ���������� ����� ��������.</font></b><br>';

      if (isset ($TarFile) and is_file ($TarFile)) unlink ($TarFile);
      if (isset ($FileDir) and is_dir  ($FileDir)) rmdir ($FileDir);
   }
}

   echo '   </div>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
         </body>';