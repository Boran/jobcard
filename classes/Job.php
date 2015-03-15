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
    $this->f3->set('message', 'Welcome to the Job System');
    $this->tpl = 'views/root.htm';
  }
  function about() {  
    $this->f3->set('message', 'Todo: General: sales rep, spec/job approve by, price/unit+kgs. Delivey:pallet params, product approved by.');
    $this->tpl = 'views/root.htm';
  }

  function find() {
    if ($this->f3->exists('POST.job')) {
      $find = $this->f3->get('POST.job');
    } else if ($this->f3->exists('GET.job')) {
      $find = $this->f3->get('GET.job');
    } else {
      // no value yet, show search  form
      $this->f3->set('warn', '');
      $this->tpl = 'views/jobsearch.htm';
      return;
    }
    $this->logger->write('in Job::found() got:' . $find);
    $this->f3->reroute("/job/$find");   // show that job
  }
  function getall() {
    $limit=$this->f3->get('joblistlimit');
    $sql = "select prodpr, prodspr, Job, PrinterLookup, Print_ref, Customer, JobStatus, Del_date1 from v_jprint where Printing='Y' order by prodpr DESC limit $limit";
    $this->f3->set('result', $this->db->exec($sql));
    $this->tpl = 'views/jobs.htm';
  }

  function get($f3, $args) {   // show one job
    //print_r($args);
    $this->job->load(array('Job=?', $args['item']));
    if ($this->job->dry()) {  // could not find it
      $this->f3->set('warn', 'Could not find job ' . $args['item']);
      $this->tpl = 'views/jobsearch.htm';
      return;
    }

    #echo 'job::get(): ' . $this->job->Job;
    $f3->set('job', $this->job);
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
