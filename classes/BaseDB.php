<?php


class BaseDB extends  \Prefab {  // singleton
  protected $f3, $logger, $db, $auth;
  protected $tpl;  // the name of the template to render.

  public function __construct() {
    $this->f3     = \Base::instance();
    $this->logger = \Registry::get('logger');
    $this->db     = \Registry::get('db');
    $this->auth   = new DB\SQL\Mapper($this->db, 'person'); // login table
  }

  function afterRoute($f3) {   // draw the page
    if (isset($this->tpl)) {
      $f3->set('tpl', $this->tpl);
      echo Template::instance()->render('views/layout.htm'); // std page
    }
  }

  function log($msg, $level=1) {
    // todo: level 1-3 ($debug in config.ini)
    $this->logger->write($msg);
    syslog(LOG_INFO, $msg);
  }


  /* 
   * authentication from mysql with hashed password
   */
  function login() {
    $salt="--"; $username='administrator'; $pw='CHANGEME';  // to do

    $auth=new \Auth($this->auth, array('id'=>'Login','pw'=>'password'));
    if ($auth->login($username, sha1($username. $salt .$pw))) {
      $this->log("authentication successful $username", 2);
      $this->f3->set('SESSION.user_id',$username);
      #$this->root();      // jump to the front page
      $this->f3->reroute('/');
    }
    else {
      $this->log("$username login failed ");
    }
  }

  function logout() {
    $this->log($this->f3->get('SESSION.user_id') . " logout");
    $this->f3->clear('SESSION.user_id');
    session_commit();
    $this->f3->set('message', 'You have been logged out', 2);
    $this->tpl = 'views/root.htm';
  }

}


?>
