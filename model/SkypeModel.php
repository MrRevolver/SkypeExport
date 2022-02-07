<?

class SkypeModel extends JSON {

   function __construct()
   {
      $this->Time  = new Time  ();
      $this->Debug = new Debug ();
   }

   function ConversationValid ($Conversation)                                    // �������� ����������� $Conversation
   {
      if (!isset ($Conversation) or empty ($Conversation)) {
         $this->Debug->Out ('�� ������� $Conversation'.print_r ($Conversation->id, true));
         return false;
      }

      if (!isset ($Conversation->id) or empty ($Conversation->id)) {
         $this->Debug->Out ('����������� id � Conversation'.print_r ($Conversation->id, true));
         return false;
      }

      if (!isset ($Conversation->MessageList) or empty ($Conversation->MessageList)) {
         $this->Debug->Out ('����������� MessageList � Conversation');
         $this->Debug->Dump ($Conversation->MessageList);
         return false;
      }

      if ($Conversation->properties->conversationblocked == true) {
         $this->Debug->Out ('��� Conversation ������������');
         $this->Debug->Dump ($Conversation->properties);
         return false;
      }

      if (!$Conversation->threadProperties == null) {                            // ��������, ����������� ��� �����������
         $this->Debug->Out ('��� Thread');
         $this->Debug->Dump ($Conversation->threadProperties);
         return false;
      }

      return true;
   }

   function MessageValid ($Message)                                              // �������� ����������� $Message
   {
      if (!isset ($Message) or empty ($Message)) {
         $this->Debug->Out ('�� �������� $Message'.print_r ($Message, true));
         return false;
      }

      if (!isset ($Message->originalarrivaltime) or empty ($Message->originalarrivaltime)) {
         $this->Debug->Out ('����������� originalarrivaltime � ���������'.print_r ($Message, true));
         return false;
      }

      if (!isset ($Message->id) or empty ($Message->id)) {
         $this->Debug->Out ('����������� id � ���������'.print_r ($Message, true));
         return false;
      }

      if (!isset ($Message->messagetype) or empty ($Message->messagetype)) {
         $this->Debug->Out ('����������� messagetype � ���������'.print_r ($Message, true));
         return false;
      }

      if (!isset ($Message->content) or empty ($Message->content)) {
         $this->Debug->Out ('����������� content � ���������');
         $this->Debug->Dump ($Message);
         return false;
      }

      if (!isset ($Message->conversationid) or empty ($Message->conversationid)) {
         $this->Debug->Out ('����������� conversationid � ���������'.print_r ($Message, true));
         return false;
      }

      if (!isset ($Message->from) or empty ($Message->from)) {
         $this->Debug->Out ('����������� from � ���������'.print_r ($Message, true));
         return false;
      }

      if (isset ($Message->properties->isserversidegenerated)) {                 // ������ ������ ������������������ ���������
         $this->Debug->Out ('������ ��������� ���� ���������������');
         $this->Debug->Dump ($Message);
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
            $this->Debug->Out ('������������ messagetype:<br>');
            $this->Debug->Dump ($Message);
            $this->Stat['Ignore']++;
            break;

         default:
            $this->Debug->Out ('��������! ����� ��� ������! �������� ������������<br>');
            $this->Debug->Dump ($Message);
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

   function ProcessConversation ($JsonConversation) 
   {
      $Conversation = [];

      if ($this->ConversationValid($JsonConversation)) {                // ���� Conversation ������ ��������

         $Conversation['ConversationId'] = $this->ExplodeSkype($JsonConversation->id);
         $Conversation['Name']           = $JsonConversation->displayName;
         $Conversation['isBloced']       = $JsonConversation->properties->conversationblocked;
         $Conversation['Thread']         = $JsonConversation->threadProperties;

         $Id_SkypeExcludeState = null;

         if ($Id_SkypeExcludeState != 'Ignore') {                        // ��������� ������ "�� ���������"

            foreach ($JsonConversation->MessageList as $JsonMessage) {

               if (isset($JsonMessage)) {

                  $Message = $this->ProcessMessage($JsonMessage);
                  if ($Message) $Conversation['MessageList'][] = $Message;
               }
            }
         } else {
            $this->Debug->Out('� ���� ��������� ������ "�� ���������"');
            $this->Stat['Ignore']++;
         }

         return $Conversation;
      }

      return false;
   }

   function ProcessMessage ($JsonMessage) 
   {
      if ($this->MessageValid ($JsonMessage)) {

         $MessageType = $this->MessageType ($JsonMessage);

         if (!is_null ($MessageType)) {

            $Message = ['MessageType'  => $MessageType,
                        'ID'           => $JsonMessage->id,
                        'Conversation' => $JsonMessage->conversationid,
                        'Name'         => $JsonMessage->displayName,
                        'Content'      => $JsonMessage->content,
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
}
