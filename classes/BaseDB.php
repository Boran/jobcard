<?php

/*
 * This base class takes care of common controller functions such as
 * DB connections, logging, login/out, and calling of views.
 * It also has some "model" chatacteristics since it has links to the user table
 *
 */

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
   * Should this be in a separate class?
   */
  function login() {
    $this->tpl = 'views/login.htm';
  }

  function processlogin() {
    $salt="--"; 
    #$username='administrator'; $pw='CHANGEME';  // to do
    $username=$this->f3->get('POST.loginUser');
    $pw=$this->f3->get('POST.loginPass');

    $auth=new \Auth($this->auth, array('id'=>'Login','pw'=>'password'));
    if ($auth->login($username, sha1($username. $salt .$pw))) {
      $this->log("authentication successful $username", 2);
      $this->f3->set('SESSION.user_id',$username);
      $this->f3->reroute('/'); // jump to the front page
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
