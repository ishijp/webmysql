<?php
{
  require_once("config.php");

  require_once(LBDIR . "/libs/Smarty.class.php");

  function pre01($buff, &$this) {
    return mb_convert_encoding($buff,"EUC-JP","SJIS");
  }

  function post01($buff, &$this) {
    return mb_convert_encoding($buff,"SJIS","EUC-JP");
  }

  class MySmarty extends Smarty {
    var $_db;

    function MySmarty() {
      // Register destructor (PHP4)
      register_shutdown_function(array(&$this, '_MySmarty'));

      //$this->Smarty();
      $this->template_dir = SBDIR . "/" . "templates";
      $this->compile_dir = SBDIR . "/" . "templates_c";
      $this->config_dir = SBDIR . "/" . "config";
      $this->cache_dir = SBDIR . "/" . "cache";

      // For Shift-JIS charset handling
      //$this->register_prefilter('pre01');
      //$this->register_postfilter('post01');

    }

    // Destructor
    function _MySmarty() {
      // So far, nothing to do.
    }
  }

  //
    if (empty($sm)) {
      $sm = new MySmarty;
    }

}
?>
