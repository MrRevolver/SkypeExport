<?

class SkypeView {

   function __construct()
   {
      $this->Debug = new Debug ();
   }

   function Icon ($Text)
   {
      return iconv('UTF-8', 'windows-1251//IGNORE', $Text);
   }

   function CodeConversations ($Conversations)
   {
      $Code = '';
      $Id = 0;

      $Code .= '      <div class="row mt-20">'.
               '         <div class="col-3 col-xxl-2 flex-column">'.
               '            <div class="row scroll-list" style="padding-bottom: 50px;">'.
               '               <div class="col nav nav-pills" style="padding-right: 0px; display: block;" id="v-pills-tab" role="tablist">';

      foreach ($Conversations as $ConvNumber => $ConversationData) {

         $ConversationData['Name'] = $this->Icon ($ConversationData['Name']);

         $Id++;

         if ($Id == 1) $Class = 'active';
         else          $Class = '';

         $Code .= '               <div class="d-flex" id="'.$ConvNumber.'">'.
                  '                  <input class="form-check-input" type="checkbox" value="" id="flexCheck'.$ConvNumber.'" onclick="CheckOption (this);">'.
                  '                  <a class="nav-link '.$Class.' flex-fill" id="v-pills-'.$ConvNumber.'-tab" href="#v-pills-'.$ConvNumber.'" data-bs-toggle="pill" role="tab" onclick="ResetActiveNavLink (this)">'.
                                     (!empty ($ConversationData['Name']) ? $ConversationData['Name'] : $ConversationData['ConversationId']).'<br>'.
                  '                     <span class="text-muted message-label">'.$ConversationData['ConversationId'].'</span>'.
                  '                  </a>'.
                  '               </div>';
      }

      $Code .= '               </div>'.
               '            </div>'.
               '         </div>';

      return $Code;
   }

   function CodeMessages ($Conversations, $Limit = 50)
   {
      $Code = '';
      $Id = 0;
      $CurrentTime = 0;

      $Code .= '         <div class="col-9 col-xxl-10">'.
               '            <div class="tab-content rounded" id="v-pills-tabContent">';

      foreach ($Conversations as $ConvNumber => $ConversationData) {

         $ConversationData['Name'] = $this->Icon ($ConversationData['Name']);

         $Id++;

         if ($Id == 1) $Class = 'show active';
         else          $Class = '';

         $Code .= '         <div class="tab-pane fade '.$Class.'" id="v-pills-'.$ConvNumber.'" data-bs-toggle="tab" role="tabpanel">'.
                  '            <div class="row" style="margin-right: 0px;">'.
                  '               <div class="col">'.
                  '                  <div class="row">'.
                  '                     <div class="col border-bottom border-dark h3 pb-3 pt-3">'.(!empty ($ConversationData['Name']) ? $ConversationData['Name'] : $ConversationData['ConversationId']). '</div>'.
                  '                  </div>'.
                  '               </div>'.
                  '            </div>'.
                  '            <div class="row scroll-list message-list" style="height: 83.5%; padding-bottom: 90px;">'.
                  '               <div class="col" style="padding-left: 0px;">';

         if (empty ($ConversationData['MessageList'])) {

            $Code .= '                  <div class="text-start">Список сообщени пуст</div>';
         }
         else {

            foreach ($ConversationData['MessageList'] as $i => $Message) {

               $Message['Content'] = $this->Icon ($Message['Content']);
               $Message['Name']    = $this->Icon ($Message['Name']);

               $FullName = explode (' ', $Message['Name']);

               if ($CurrentTime != Time::FormatDate ($Message['DMessage'], 'd.m.Y')) {

                  $Code .= '                  <div class="row" style="align-items: center; margin: 0px;">'.
                           '                     <div class="col-sm-5" style="height:1px; background-color:#dee2e6;"></div>'.
                           '                     <div class="col-sm-2 text-center text-muted message-label">'.Time::FormatDateToText($Message['DMessage']). '</div>'.
                           '                     <div class="col-sm-5" style="height:1px; background-color:#dee2e6;"></div>'.
                           '                  </div>';
                  $CurrentTime = Time::FormatDate ($Message['DMessage'], 'd.m.Y');
               }

               if ($Message['From'] == $ConversationData['ConversationId']) {

                  $Code .= '                  <div class="text-start text-muted message-label">'.(isset ($FullName[0]) ? $FullName[0] : $Message['Name']).' '.Time::FormatDate ($Message['DMessage'], 'H:i').'</div>'.
                           '                  <div class="text-start pb-1"><span class="py-2 px-3 d-inline-block rounded text-wrap text-break message" style="background: #f2f6f9">'.$Message['Content'].'</span></div>';
               }
               else {

                  $Code .= '                  <div class="text-end text-muted message-label">'.Time::FormatDate ($Message['DMessage'], 'H:i').'</div>'.
                           '                  <div class="text-end pb-1"><span class="py-2 px-3 d-inline-block rounded text-wrap text-break message" style="background: #e3f2fd">'.$Message['Content'].'</span></div>';

               }

               if (isset ($Limit) && $i >= $Limit) {
                  break;
               }
            }
         }

         $Code .= '               </div>'.
                  '            </div>'.
                  '         </div>';
      }

      $Code .= '      </div>'.
               '   </div>'.
               '</div>';

      return $Code;
   }

   function WriteCode ($Messages) {

      echo $this->CodeConversations ($Messages);
      echo $this->CodeMessages      ($Messages);
   }
}

