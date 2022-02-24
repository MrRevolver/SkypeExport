<?

class Debug {

   private $IsOn = false;
   private $ErrorLevel;

   function __construct ($IsOn = '')
   {
      if ($IsOn === '') $this->IsOn = (isset ($_GET['IsDebug']));
      else              $this->IsOn = $IsOn;

      if ($this->IsOn) $this->Set ();
   }

   function Is ()
   {
      return $this->IsOn;
   }

   function On ()
   {
      if (!$this->IsOn) $this->Set ();
      $this->IsOn = true;
   }

   function Off ()
   {
      if ($this->IsOn) $this->SetOff ();
      $this->IsOn = false;
   }

   function Set ($Level = false)
   {
      ini_set ('display_errors', '1');
      ini_set ('display_startup_errors', '1');
      ini_set ('html_errors', '1');

      $this->ErrorLevel = error_reporting (($Level === false)? (E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT): $Level);
   }

   function SetOff ()
   {
      ini_set ('display_errors', '0');
      ini_set ('display_startup_errors', '0');
      ini_set ('html_errors', '0');

      error_reporting ($this->ErrorLevel);
   }

   function Out ($Text)
   {
      //if ($this->IsOn) echo $Text;
   }

   function Dump ($Text)
   {
      // if ($this->IsOn) {
      //    echo '<pre>';
      //    var_dump ($Text);
      //    echo '</pre>';
      // }
   }

   function Err ($Text)
   {
      if ($this->IsOn) {
         echo '<pre>';
         var_dump($Text);
         echo '</pre>';
      }
   }
}

$Debug = new Debug ();
