<?php

class Job extends BaseDB {
  protected $job, $tpl;
  protected $table='v_jprint';

  public function __construct() {
    parent::__construct();
    $this->db = \Registry::get('db');
    $this->job=new DB\SQL\Mapper($this->db, $this->table);
  }

  function beforeRoute() {
    #echo 'in Job::beforeRoute()>';
    #$this->logger->write('in Job::beforeRoute()');
  }
  //function afterRoute($f3) { 
  // see parent: this is where the page is drawn, based on $tpl
  //}

  function root() {   // handle /
    $this->tpl = 'views/root.htm';
  }

  function find() {
    if ($this->f3->exists('POST.job')) {
      $find = $this->f3->get('POST.job');
    } else if ($this->f3->exists('GET.job')) {
      $find = $this->f3->get('GET.job');
    } else {
      // no value yet, show search  form
      $this->tpl = 'views/jobsearch.htm';
      return;
    }
    echo "<h3>find=$find</h3>";
    $this->logger->write('in Job::found() got:' . $find);
    $this->f3->reroute("/job/$find");   // show that job
  }
  function getall() {
      $this->f3->set('result', $this->db->exec('SELECT * FROM jobcard limit 3'));
      $this->tpl = 'views/jobs.htm';
  }

  function get($f3, $args) {   // show one job
      //print_r($args);
      $this->job->load(array('Job=?', $args['item']));
      if ($this->job->dry()) {
        echo 'Could not find job ' . $args['item'];
        // todo: jump to find()
        //$this->f3->reroute("/find");
        return;
      }

      #echo 'job::get(): ' . $this->job->Job;
      $this->job->anilox1='-';  // todo
      $this->job->anilox2='-';
      $this->job->anilox3='-';
      $this->job->anilox4='-';
      $this->job->anilox5='-';
      $this->job->anilox6='-';
      $this->job->anilox7='-';
      $this->job->anilox8='-';
      $f3->set('job', $this->job);
      #echo Template::instance()->render('views/job2.htm');
      $this->tpl = 'views/job.htm';
  }
  function post() {
  }
  function put() {
  }
  function delete() {
  }
}


?>
