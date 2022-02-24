<?

class Time {

   public static function GetTimeStamp ($Str, $Format = 'Y.m.d H:i:s')
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
            die ('Неверный формат даты');
      }

      return mktime ($Hour, $Minute, $Second, $Month, $Day, $Year);
   }

   public static function FormatDate ($Str, $FormatOut = 'd.m.Y', $FormatIn = 'Y.m.d H:i:s')
   {
      if (is_null ($Str)) return null;
      return date ($FormatOut, self::GetTimeStamp ($Str, $FormatIn));
   }

   public static function FormatDateToText ($Date, $FormatIn = 'Y.m.d H:i:s', $FormatOut = 'd m Y')
   {
      if (is_null ($Date)) return null;

      $Month[ 1] = "Января";
      $Month[ 2] = "Февраля";
      $Month[ 3] = "Марта";
      $Month[ 4] = "Апреля";
      $Month[ 5] = "Мая";
      $Month[ 6] = "Июня";
      $Month[ 7] = "Июля";
      $Month[ 8] = "Августа";
      $Month[ 9] = "Сентября";
      $Month[10] = "Октября";
      $Month[11] = "Ноября";
      $Month[12] = "Декабря";

      $TimeStamp = self::GetTimeStamp ($Date, $FormatIn);

      $Out = $FormatOut;
      $Out = str_replace ('d', date ('d', $TimeStamp)                  , $Out);
      $Out = str_replace ('m', $Month [intval (date ('m', $TimeStamp))], $Out);
      $Out = str_replace ('Y', date ('Y', $TimeStamp)                  , $Out);

      return $Out;
  }
}