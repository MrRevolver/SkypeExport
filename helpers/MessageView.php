<?
//ini_set ('display_errors', '1');
//error_reporting (E_ALL);

$Mails  = array ();
$Phones = array ();
$Keys   = array ();

?>
<table class="main">
   <tr><td><? $this->Id_SkypeExcludeState->SayTitle ()  ?></td><td><? $this->Id_SkypeExcludeState->Say ()  ?></td></tr>
   <tr><td><? $this->Id_User->SayTitle ()               ?></td><td><? $this->Id_User->Say ()               ?></td></tr>
   <tr><td><? $this->Identity->SayTitle ()              ?></td><td><? $this->Identity->Say ()              ?></td></tr>
   <tr><td><? $this->Reason->SayTitle ()                ?></td><td><? $this->Reason->Say ()                ?></td></tr>
   <tr><td colspan="2">Список сообщений:<br>
<?
   echo '<div style="max-height: 700px; overflow-y: scroll; word-break: break-word; overflow-wrap: break-word;"><ul style="list-style: none; padding-left: 5px;"';

   foreach ($Messages as $MessageRow) {

      echo '<li>'.FormatDateTime ($MessageRow['DSkype']).' '.$MessageRow['Alias'].' '.$MessageRow['Message'];

      $Content = $MessageRow['Message'];
      $Content = strip_tags ($Content);
      $Content = preg_replace ('/\[\d+\]/', '', $Content);
                                                                                 // Собираем возможные контактные данные
      if (preg_match_all ("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $Content, $MailsRow ) > 0) $Mails  = array_merge ($Mails , $MailsRow[0]);
      if (preg_match_all ("/([+\d(]{1}[-0-9\s()]{6,}[\d]{1})/"  , $Content, $PhonesRow) > 0) $Phones = array_merge ($Phones, $PhonesRow[0]);
      if (preg_match_all ("/(?:[A-Z0-9]{5}-){4}[A-Z0-9]{5}/"    , $Content, $KeysRow  ) > 0) $Keys   = array_merge ($Keys  , $KeysRow[0]);
   }

   echo '</ul></div>';
?>
   </td></tr>
   <tr><td><?= 'Найденные данные:'?></td></tr>
<?
   $Mails  = array_unique ($Mails);
   $Phones = array_unique ($Phones);
   $Keys   = array_unique ($Keys);
                                                                              // Поиск клиента по емайлам
   foreach ($Mails as $Mail) {

      echo '<tr><td>'.$Mail.'</td></tr>';
   }
                                                                              // Поиск клиента по телефонам
   foreach ($Phones as $Phone) {

      $PhoneFind = preg_replace ('/\D/', '', $Phone);

      if (strlen ($PhoneFind) > 5) {

         echo '<tr><td>'.$Phone.' ('.$PhoneFind.')</td></tr>';
      }
   }
?>
</table>
