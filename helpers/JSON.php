<?

class JSON {

   public static function Decode ($In)
   {
      return json_decode ($In);
   }

   public static function Encode ($In)
   {
      return json_encode ($In, JSON_UNESCAPED_UNICODE);
   }

   public static function Valid ()                                                              // Проверка целостности JSON
   {
      $Debug = new Debug();

      switch (json_last_error ()) {

         case JSON_ERROR_NONE:
            $Debug->Out ('JSON валиден');
            return true;

         case JSON_ERROR_DEPTH:
            $Debug->Out ('JSON - Достигнута максимальная глубина стека');
            return false;

         case JSON_ERROR_STATE_MISMATCH:
            $Debug->Out ('JSON - Некорректные разряды или несоответствие режимов');
            return false;

         case JSON_ERROR_CTRL_CHAR:
            $Debug->Out ('JSON - Некорректный управляющий символ');
            return false;

         case JSON_ERROR_SYNTAX:
            $Debug->Out ('JSON - Синтаксическая ошибка, некорректный JSON');
            return false;

         case JSON_ERROR_UTF8:
            $Debug->Out ('JSON - Некорректные символы UTF-8, возможно неверно закодирован');
            return false;

         default:
            $Debug->Out ('JSON - Неизвестная ошибка');
            return false;
      }
   }
}

