<?php


class BaseDB extends  \Prefab {  // singleton
  protected $f3, $logger, $db;
  protected $tpl;  // the name of the template to render.

  public function __construct() {
    $this->f3     = \Base::instance();
    $this->logger = \Registry::get('logger');
    $this->db = \Registry::get('db');
  }

  function afterRoute($f3) {   // draw the page
    if (isset($this->tpl)) {
      $f3->set('tpl', $this->tpl);
      echo Template::instance()->render('views/layout.htm'); // std page
    }
  }

  function log($msg, $level) {
    // todo: level 1-3 ($debug in config.ini)
    $this->logger->write($msg);
    syslog(LOG_INFO, $msg);
  }

}


?>
