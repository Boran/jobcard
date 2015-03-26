<?php

/*
 * This base class takes care of common controller functions such as
 * DB connections, logging, login/out, and calling of views.
 * It also has some "model" chatacteristics since it has links to the user table
 *
 */

class BaseDB extends  \Prefab {  // singleton
  protected $f3, $logger, $db, $auth, $user_id;
  protected $tpl;  // the name of the template to render.


  public function __construct() {
    $this->f3     = \Base::instance();

    $this->f3->set('LOGS', '/tmp/');  //default is ./
    $this->logger = new \Log($this->f3->get('logfile'));
    \Registry::set('logger', $this->logger);
    $this->debug = $this->f3->get('debug');
    $this->log("BaseDB", 2);

    $this->db=new DB\SQL(sprintf("mysql:host=%s;port=3306;dbname=%s", $this->f3->get('dbhost'),  
      $this->f3->get('dbname')), $this->f3->get('dbuser'), $this->f3->get('dbpw') );
    \Registry::set('db', $this->db);

    $this->auth   = new DB\SQL\Mapper($this->db, 'person'); // login table
  }


  function beforeRoute($f3) {
    if ($this->user_id=$f3->get('SESSION.user_id')) {
      $this->log("beforeRoute: $this->user_id logged in", 2);
    }
  }

  function afterRoute($f3) {   // draw the page
    if (isset($this->tpl)) {
      $f3->set('tpl', $this->tpl);
      echo Template::instance()->render('views/layout.htm'); // std page
    }
    // finish up
    if ($this->debug > 4) {
      $this->logger->write(\Registry::get('db')->log());
    }
    if ($this->debug > 3) {
      $execution_time = round(microtime(true) - $this->f3->get('TIME'), 3);
      $this->logger->write('Executed in ' . $execution_time . ' secs using ' . round(memory_get_usage() / 1024 / 1024, 2) . '/' . round(memory_get_peak_usage() / 1024 / 1024, 2) . ' MB memory/peak');
    }
  }


  function log($msg, $level=1) {
    // todo: level 1-3 ($debug in config.ini)
    #if (($this->debug >1) && ($level>1)) {
      $this->logger->write($msg);
      syslog(LOG_INFO, $msg);
    #}
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
      $this->f3->set('SESSION.user_id', $username);
      $this->f3->reroute('/'); // OK: jump to the front page
    }
    else {
      $this->log("$username login failed ");
      #$this->f3->reroute('/login');
      #$this->f3->reroute('/'); // jump to the front page
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
