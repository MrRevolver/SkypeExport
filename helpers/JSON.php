<?

class JSON {

   function __construct()
   {
      $this->Debug = new Debug ();
   }

   function Decode ($In)
   {
      return json_decode ($In);
   }

   function Valid ()                                                          // Проверка целостности JSON
   {
      switch (json_last_error ()) {

         case JSON_ERROR_NONE:
            $this->Debug->Out ('JSON валиден');
            return true;

         case JSON_ERROR_DEPTH:
            $this->Debug->Out ('JSON - Достигнута максимальная глубина стека');
            return false;

         case JSON_ERROR_STATE_MISMATCH:
            $this->Debug->Out ('JSON - Некорректные разряды или несоответствие режимов');
            return false;

         case JSON_ERROR_CTRL_CHAR:
            $this->Debug->Out ('JSON - Некорректный управляющий символ');
            return false;

         case JSON_ERROR_SYNTAX:
            $this->Debug->Out ('JSON - Синтаксическая ошибка, некорректный JSON');
            return false;

         case JSON_ERROR_UTF8:
            $this->Debug->Out ('JSON - Некорректные символы UTF-8, возможно неверно закодирован');
            return false;

         default:
            $this->Debug->Out ('JSON - Неизвестная ошибка');
            return false;
      }
   }
}

