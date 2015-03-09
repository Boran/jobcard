<?php


class Base extends \Prefab {  // singleton
  protected $f3, $logger;

  public function __construct() {
    $this->f3     = \Base::instance();
    $this->logger = \Registry::get('logger');
  }

}


?>
