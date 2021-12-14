<?
class JSON {

   function __construct() {

      $this->Write = new Write ();
   }

   function Valid ()                                                          // �������� ����������� JSON
   {
      switch (json_last_error ()) {

         case JSON_ERROR_NONE:
            $this->Write->Out ('JSON �������');
            return true;

         case JSON_ERROR_DEPTH:
            $this->Write->OutError ('JSON - ���������� ������������ ������� �����');
            return false;

         case JSON_ERROR_STATE_MISMATCH:
            $this->Write->OutError ('JSON - ������������ ������� ��� �������������� �������');
            return false;

         case JSON_ERROR_CTRL_CHAR:
            $this->Write->OutError ('JSON - ������������ ����������� ������');
            return false;

         case JSON_ERROR_SYNTAX:
            $this->Write->OutError ('JSON - �������������� ������, ������������ JSON');
            return false;

         case JSON_ERROR_UTF8:
            $this->Write->OutError ('JSON - ������������ ������� UTF-8, �������� ������� �����������');
            return false;

         default:
            $this->Write->OutError ('JSON - ����������� ������');
            return false;
      }
   }
}

class Time {

   function GetTimeStamp ($Str, $Format = 'Y.m.d H:i:s')
   {
   $Day    = 0;
   $Month  = 0;
   $Year   = 0;
   $Hour   = 0;
   $Minute = 0;
   $Second = 0;

   switch ($Format) {

      case 'd.m.Y':
         sscanf ($Str, "%2d%*c%2d%*c%4d", $Day, $Month, $Year);
         break;

      case 'Y.m.d':
         sscanf ($Str, "%4d%*c%2d%*c%2d", $Year, $Month, $Day);
         break;

      case 'd.m.Y H:i:s':
         sscanf ($Str, "%2d%*c%2d%*c%4d%*c%2d%*c%2d%*c%2d", $Day, $Month, $Year, $Hour, $Minute, $Second);
         break;

      case 'Y.m.d H:i:s':
         sscanf ($Str, "%4d%*c%2d%*c%2d%*c%2d%*c%2d%*c%2d", $Year, $Month, $Day, $Hour, $Minute, $Second);
         break;

      default:
         die ('�������� ������ ����');
   }

      return mktime ($Hour, $Minute, $Second, $Month, $Day, $Year);
   }

   function FormatDate ($Str, $FormatOut = 'd.m.Y', $FormatIn = 'Y.m.d H:i:s')
   {
   if (is_null ($Str)) return null;
   return date ($FormatOut, $this->GetTimeStamp ($Str, $FormatIn));
   }

   function FormatDateToText ($Date, $FormatIn = 'Y.m.d H:i:s', $FormatOut = 'd m Y')
   {
      if (is_null ($Date)) return null;

      $Month[ 1] = "������";
      $Month[ 2] = "�������";
      $Month[ 3] = "�����";
      $Month[ 4] = "������";
      $Month[ 5] = "���";
      $Month[ 6] = "����";
      $Month[ 7] = "����";
      $Month[ 8] = "�������";
      $Month[ 9] = "��������";
      $Month[10] = "�������";
      $Month[11] = "������";
      $Month[12] = "�������";

      $TimeStamp = $this->GetTimeStamp ($Date, $FormatIn);

      $Out = $FormatOut;
      $Out = str_replace ('d', date ('d', $TimeStamp)                  , $Out);
      $Out = str_replace ('m', $Month [intval (date ('m', $TimeStamp))], $Out);
      $Out = str_replace ('Y', date ('Y', $TimeStamp)                  , $Out);

      return $Out;
  }
}

class Write {

   function Out ($Text)                                                          // ��� ������ ������
   {
      //echo $Text;
   }

   function OutData ($Var)                                                       // ����� ������
   {
      /*
      if (is_object($Var) && isset ($Var->content)) {

         if (!empty ($Var->displayName)) {

            $Var->displayName = iconv ('UTF-8', 'windows-1251//IGNORE', $Var->displayName);
         }

         if (!empty ($Var->content)) {

            $Var->content = iconv ('UTF-8', 'windows-1251//IGNORE', $Var->content);
         }
      }

      echo '<pre>'.print_r($Var, true).'</pre>';
      */
   }

   function OutError ($Text)                                                     // ����� ������ ���������
   {
      echo '<b><font color="red"><pre>������: '.$Text.'</pre></font></b>';
   }

   function OutWrite ($Text)                                                     // ����� ������
   {
      echo '<pre>'.$Text.'</pre>';
   }
}

class Skype {

   public  $IsWrite = true;                                                      // ����� ������

   private $Conversations = [];
   private $Stat = ['NotFound'   => 0,
                    'Exists'     => 0,
                    'NewMessage' => 0,
                    'Ignore'     => 0];

   function __construct() {

      $this->Time  = new Time  ();
      $this->JSON  = new JSON  ();
      $this->Write = new Write ();
   }

   function ConversationValid ($Conversation)                                    // �������� ����������� $Conversation
   {
      if (!isset ($Conversation) or empty ($Conversation)) {
         $this->Write->OutError ('�� ������� $Conversation'.print_r ($Conversation->id, true));
         return false;
      }

      if (!isset ($Conversation->id) or empty ($Conversation->id)) {
         $this->Write->OutError ('����������� id � Conversation'.print_r ($Conversation->id, true));
         return false;
      }

      if (!isset ($Conversation->MessageList) or empty ($Conversation->MessageList)) {
         $this->Write->Out ('����������� MessageList � Conversation');
         $this->Write->OutData ($Conversation->MessageList);
         return false;
      }

      if ($Conversation->properties->conversationblocked == true) {
         $this->Write->Out ('��� Conversation ������������');
         $this->Write->OutData ($Conversation->properties);
         return false;
      }

      if (!$Conversation->threadProperties == null) {                            // ��������, ����������� ��� �����������
         $this->Write->Out ('��� Thread');
         $this->Write->OutData ($Conversation->threadProperties);
         return false;
      }

      return true;
   }

   function MessageValid ($Message)                                              // �������� ����������� $Message
   {
      if (!isset ($Message) or empty ($Message)) {
         $this->Write->OutError ('�� �������� $Message'.print_r ($Message, true));
         return false;
      }

      if (!isset ($Message->originalarrivaltime) or empty ($Message->originalarrivaltime)) {
         $this->Write->OutError ('����������� originalarrivaltime � ���������'.print_r ($Message, true));
         return false;
      }

      if (!isset ($Message->id) or empty ($Message->id)) {
         $this->Write->OutError ('����������� id � ���������'.print_r ($Message, true));
         return false;
      }

      if (!isset ($Message->messagetype) or empty ($Message->messagetype)) {
         $this->Write->OutError ('����������� messagetype � ���������'.print_r ($Message, true));
         return false;
      }

      if (!isset ($Message->content) or empty ($Message->content)) {
         $this->Write->Out ('����������� content � ���������');
         $this->Write->OutData ($Message);
         return false;
      }

      if (!isset ($Message->conversationid) or empty ($Message->conversationid)) {
         $this->Write->OutError ('����������� conversationid � ���������'.print_r ($Message, true));
         return false;
      }

      if (!isset ($Message->from) or empty ($Message->from)) {
         $this->Write->OutError ('����������� from � ���������'.print_r ($Message, true));
         return false;
      }

      if (isset ($Message->properties->isserversidegenerated)) {                 // ������ ������ ������������������ ���������
         $this->Write->Out ('������ ��������� ���� ���������������');
         $this->Write->OutData ($Message);
         return false;
      }

      return true;
   }

   function MessageType ($Message)
   {
      switch ($Message->messagetype) {

         case 'Text':
            return 1;
         case 'Event/Call':
            return 2;
         case 'RichText':
            return 3;

         case 'RichText/Media_GenericFile':
         case 'RichText/Media_Video':
         case 'RichText/Media_Album':
         case 'RichText/Media_Card':
         case 'RichText/Media_CallRecording':
         case 'RichText/Contacts':
         case 'ThreadActivity/AddMember':
         case 'RichText/UriObject':
         case 'RichText/Media_AudioMsg':
         case 'RichText/Media_GenericFile':
         case 'ThreadActivity/TopicUpdate':
         case 'ThreadActivity/HistoryDisclosedUpdate':
         case 'InviteFreeRelationshipChanged/Initialized':
         case 'ThreadActivity/JoiningEnabledUpdate':
         case 'RichText/Files':
         case 'ThreadActivity/DeleteMember':
         case 'PopCard':
         case 'Notice':
            $this->Write->Out ('������������ messagetype:<br>');
            $this->Write->OutData ($Message);
            $this->Stat['Ignore']++;
            break;

         default:
            $this->Write->OutError ('��������! ����� ��� ������! �������� ������������<br>');
            $this->Write->OutData ($Message);
            $this->Stat['Ignore']++;
      }
   }

   function DMessage ($Time, $Interval)                                          // ��������� ���� � ������� ���������. ��������� 0 � $Interval, ���� ����� ������ ��������������.
   {
      $DMessage = new DateTime ($this->Time->FormatDate ($Time, "Y-m-d H:i:s"));
      $DMessage->add (new DateInterval ('PT7H0M0S'));

      if ($Interval > 0) {
         $DMessage->add (new DateInterval('PT'.$Interval.'M0S'));
      } else $DMessage->sub (new DateInterval('PT'.abs ($Interval).'M0S'));

      return $DMessage->format ('Y-m-d H:i:s');
   }

   function ExplodeSkype ($Id)                                                   // ��������� ������ Skype �� ����������� "��� ������:�����" ��� "��� ������:live:�����"
   {
      $Find = explode (':', $Id);

      if ($Find[1] == 'live') $Skype = $Find[1].':'.$Find[2] ;
      else                    $Skype = $Find[1];

      return $Skype;
   }

   function GetDuration ($Content)                                               // ��������� ����������������� ������
   {
      if (preg_match ('/\<duration\>(\d+)\<\/duration\>/', $Content, $Find)) return $Find[1];

      return null;
   }

   function ProcessMessage ($JsonMessage)
   {
      if ($this->MessageValid ($JsonMessage)) {

         $MessageType = $this->MessageType ($JsonMessage);

         if (!is_null ($MessageType)) {

            $Message = ['MessageType'  => $MessageType,
                        'ID'           => $JsonMessage->id,
                        'Conversation' => $JsonMessage->conversationid,
                        'Name'         => iconv ('UTF-8', 'windows-1251//IGNORE', $JsonMessage->displayName),
                        'Content'      => iconv ('UTF-8', 'windows-1251//IGNORE', $JsonMessage->content),
                        'Duration'     => null,
                        'DMessage'     => $this->DMessage ($JsonMessage->originalarrivaltime, 0),
                        'From'         => $this->ExplodeSkype ($JsonMessage->from)];

            if (substr ($Message['Content'], 0, 9) == '<partlist') {

               $Duration = $this->GetDuration ($Message['Content']);
            }

             return $Message;
         }
      }

      return false;
   }

   function CodeConversations ($Conversations)
   {
      $Code = '';
      $Id = 0;

      $Code .= '<div class="row">
                  <div class="col-3">
                     <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">';

      $ConversationsId = array_keys ($Conversations);

      foreach ($ConversationsId as $ConversationId) {

         $Id++;

         if ($Id == 1) $Class = 'active navbar-light';
         else          $Class = '';

         $Code .= '<a class="nav-link '.$Class.'" id="v-pills-'.$Id.'-tab" href="#v-pills-'.$Id.'" data-bs-toggle="pill" role="tab">
                  '.$ConversationId.'</a>';
      }

      $Code .= '</div></div>';

      return $Code;
   }

   function CodeMessages ($Conversations)  // ������ ��������� � ���� SkypeExcludeMessage
   {
      $Code = '';
      $Id = 0;

      $Code .= '<div class="col-9">
                  <div class="tab-content shadow p-3 rounded" id="v-pills-tabContent">';

      foreach ($Conversations as $ConversationId => $ConversationData) {

         $Id++;

         if ($Id == 1) $Class = 'show active';
         else          $Class = '';

         $Code .= '<div class="tab-pane fade '.$Class.'" id="v-pills-'.$Id. '" data-bs-toggle="tab" role="tabpanel">
                   <div class="border-bottom h3 pb-3">'.$ConversationData['Name'].'</div>';

         if (empty ($ConversationData['MessageList'])) {

            $Code .= '<div class="text-start">������ �������� ����</div>';
         }
         else {

            foreach ($ConversationData['MessageList'] as $Message) {

               if ($Message['From'] == $ConversationId) {

                  $Code .= '<div class="text-start">'.$Message['Name'].' '.$this->Time->FormatDate ($Message['DMessage'], 'H:i:s'). '</div>
                            <div class="py-2 px-3"><span style="background: #f8f9fa" class="py-2 px-3">'.$Message['Content']. '</span></div>';
               }
               else {

                  $Code .= '<div class="text-end py-2 px-3"><span class="py-2 px-3" style="background: #e3f2fd">'.$Message['Content']. '</span></div>';
               }
            }
         }

         $Code .= '</div>';
      }

      $Code .= '</div></div></div>';

      return $Code;
   }

   function WriteCode ($Messages) {

      echo $this->CodeConversations ($Messages);
      echo $this->CodeMessages      ($Messages);
   }

   function Process ($In)
   {
      $Json = json_decode ($In);

      if (!is_null ($Json)) {

         if ($this->JSON->Valid ($In)) {

            $UserSkype = $this->ExplodeSkype ($Json->userId);                    // �������� ����� ������������ �� JSON

            if (isset ($UserSkype)) {

               foreach ($Json->conversations as $Conversation) {                 // �������� � ����� �������

                  if ($this->ConversationValid ($Conversation)) {                // ���� Conversation ������ ��������

                     //die(var_dump($Conversation));
                     $Id_Conversation = $this->ExplodeSkype ($Conversation->id);          // �������� Skype ����� Conversation
                     $this->Conversations[$Id_Conversation]['Name'] = iconv ('UTF-8', 'windows-1251//IGNORE', $Conversation->displayName);
                     $this->Conversations[$Id_Conversation]['isBloced'] = $Conversation->properties->conversationblocked;
                     $this->Conversations[$Id_Conversation]['Thread'] = $Conversation->threadProperties;

                     $Id_SkypeExcludeState = null;

                     if ($Id_SkypeExcludeState != 'Ignore') {                        // ��������� ������ "�� ���������"

                        foreach ($Conversation->MessageList as $JsonMessage) {

                           if (isset ($JsonMessage)) {

                              $Message = $this->ProcessMessage ($JsonMessage);
                              if ($Message) $this->Conversations[$Id_Conversation]['MessageList'][] = $Message;
                           }
                        }
                     }
                     else {
                        $this->Out->OutWrite ('� ���� ��������� ������ "�� ���������"');
                        $this->Stat['Ignore']++;
                     }
                  }
               }

               $this->WriteCode ($this->Conversations);

               echo '<br>������� ������ ���������: '.$this->Stat['Exists'].'<br>';
               echo '�� ������� ������ ���������: '.$this->Stat['NotFound'].'<br>';
               echo '����� ���������: '.$this->Stat['NewMessage'].'<br>';
               echo '��������������� ���������: '.$this->Stat['Ignore'].'<br>';
               echo '����� ���������: '.array_sum ($this->Stat).'<br>';

            } else $this->Out->OutError ('�� ���������� �� ���� JSON ��� �� �� ������������. ������ ����������.');
         } else $this->Out->OutError ('������ ��������� JSON. ������ ����������.');
      } else $this->Out->OutError ('������ ������������� JSON: ������ ������.');

      return true;
   }
}