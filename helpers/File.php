<?

class File {

   public static function CreateFile ($Patch, $Data)
   {
      $File = fopen ($Patch, 'w');
      fwrite ($File, $Data);
      fclose ($File);
   }

   public static function CreateZip ($Patch, $File)
   {
      if (file_exists ($File)) {

         $zipArchive = new ZipArchive ();
         $zipArchive->open ($Patch,  ZipArchive::CREATE);
         $zipArchive->addFile ($File);
         $zipArchive->close ();
         unlink ($File);
      } else $this->Debug->Err ('Create Zip Error: file not found');
   }
}