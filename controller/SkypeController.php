<?

class SkypeController {

   private $User;
   private $Conversations = [];
   private $Stat = ['NotFound'   => 0,
                    'Exists'     => 0,
                    'NewMessage' => 0,
                    'Ignore'     => 0];

   function __construct()
   {
      $this->Debug = new Debug ();
      $this->View  = new SkypeView ();
      $this->Model = new SkypeModel ();
   }

   function Process ($In)
   {
      $Json = $this->Model->Decode ($In);

      if (!is_null ($Json)) {

         // $this->Debug->Err ($Json);

         if ($this->Model->Valid ($In)) {

            $this->User = $this->Model->ExplodeSkype ($Json->userId);                    // �������� ����� ������������ �� JSON

            if (isset ($this->User)) {

               foreach ($Json->conversations as $ConvNumder => $JsonConversation) {                 // �������� � ����� �������

                  $Conversation = $this->Model->ProcessConversation($JsonConversation);
                  if ($Conversation) $this->Conversations[$ConvNumder] = $Conversation;
               }
               //$this->Debug->Err($this->Conversations);

               $_SESSION = ['User' => $this->User,
                   'Conversations' => $this->Conversations];
               
               //var_dump($_SESSION);
               $this->View->WriteCode($this->Conversations);

//               echo '<br>������� ������ ���������: '.$this->Stat['Exists'].'<br>';
//               echo '�� ������� ������ ���������: '.$this->Stat['NotFound'].'<br>';
//               echo '����� ���������: '.$this->Stat['NewMessage'].'<br>';
//               echo '��������������� ���������: '.$this->Stat['Ignore'].'<br>';
//               echo '����� ���������: '.array_sum ($this->Stat).'<br>';

            } else $this->Debug->Out ('�� ���������� �� ���� JSON ��� �� �� ������������. ������ ����������.');
         } else $this->Debug->Out ('������ ��������� JSON. ������ ����������.');
      } else $this->Debug->Out ('������ ������������� JSON: ������ ������.');

      return true;
   }

   function ProcessExport(array $Select, $Format)
   {
      switch ($Format) {
         case 'json':

            $Export['User'] = $_SESSION['User'];

            foreach ($Select as $Elem) {
               foreach ($_SESSION['Conversations'] as $ConvNumber => $Conversation) {
                  if ($ConvNumber == $Elem) $Export['Conversations'][$ConvNumber] = $Conversation;
               }
            }

            $Export = json_encode($Export, JSON_UNESCAPED_UNICODE);
            break;

         case 'txt':
            
            $Export = "";

            foreach ($Select as $Elem) {
               foreach ($_SESSION['Conversations'] as $ConvNumber => $Conversation) {
                  if ($ConvNumber == $Elem) {
                     $Export .= "ConversationId ".$Conversation['ConversationId']."\n".
                                "Name ".$this->View->Icon($Conversation['Name'])."\n";
                     foreach ($Conversation['MessageList'] as $i => $Message) {
                        $Export .= "ID ".$Message['ID']."\n".
                                   "From ".$Message['From']."\n".
                                   "Name ".(!empty($Message['Name']) ? $this->View->Icon($Message['Name']) : $Message['From'])."\n".
                                   "Content ".$this->View->Icon($Message['Content'])."\n".
                                   "Duration ".$Message['Duration']."\n".
                                   "DMessage ".$Message['DMessage']."\n";
                     }
                  }
               }
            }
            break;

         case 'csv':
            
            $Export = "ConversationId;Name;ID;From;Name;Content;Duration;DMessage\n";

            foreach ($Select as $Elem) {
               foreach ($_SESSION['Conversations'] as $ConvNumber => $Conversation) {
                  if ($ConvNumber == $Elem) {
                     $Export .= $Conversation['ConversationId'].';'.$this->View->Icon ($Conversation['Name']).';';
                     foreach ($Conversation['MessageList'] as $i => $Message) {
                        if ($i > 0) $Export .= ";;";
                        $Export .= $Message['ID'].';'. $Message['From'].';'. (!empty ($Message['Name']) ? $this->View->Icon($Message['Name']) : $Message['From']).';'. $this->View->Icon($Message['Content']).';'.$Message['Duration'].';'.$Message['DMessage']."\n";
                     }
                  }
               }
            }
            break;

         default:
            echo 'default';
      }

      $ExportFile = fopen ('download/'.$_SESSION['User'].'.'. $Format, 'w');
      fwrite($ExportFile, $Export);
      fclose($ExportFile);
   }
}