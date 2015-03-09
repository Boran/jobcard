<?php


class BaseDB extends  \Prefab {  // singleton
  protected $f3, $logger, $db;

  public function __construct() {
    $this->f3     = \Base::instance();
    $this->logger = \Registry::get('logger');
    $this->db = \Registry::get('db');
  }
}


?>
