<?

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