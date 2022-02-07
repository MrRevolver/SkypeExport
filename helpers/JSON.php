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

   function Valid ()                                                          // �������� ����������� JSON
   {
      switch (json_last_error ()) {

         case JSON_ERROR_NONE:
            $this->Debug->Out ('JSON �������');
            return true;

         case JSON_ERROR_DEPTH:
            $this->Debug->Out ('JSON - ���������� ������������ ������� �����');
            return false;

         case JSON_ERROR_STATE_MISMATCH:
            $this->Debug->Out ('JSON - ������������ ������� ��� �������������� �������');
            return false;

         case JSON_ERROR_CTRL_CHAR:
            $this->Debug->Out ('JSON - ������������ ����������� ������');
            return false;

         case JSON_ERROR_SYNTAX:
            $this->Debug->Out ('JSON - �������������� ������, ������������ JSON');
            return false;

         case JSON_ERROR_UTF8:
            $this->Debug->Out ('JSON - ������������ ������� UTF-8, �������� ������� �����������');
            return false;

         default:
            $this->Debug->Out ('JSON - ����������� ������');
            return false;
      }
   }
}

