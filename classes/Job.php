<?php

class Job extends BaseDB {
  protected $job;
  protected $table='v_jprint';

  public function __construct() {
    parent::__construct();
    $this->db = \Registry::get('db');
    $this->job=new DB\SQL\Mapper($this->db, $this->table);
  }

    function beforeRoute() {
      #echo 'in Job::beforeRoute()>';
    }
    function display() {
      //echo 'job::display(): ' . $this->job->Job;
      // several entries
      $f3=$this->f3;
      $f3->set('result',$this->db->exec('SELECT * FROM jobcard limit 3'));
      echo Template::instance()->render('views/jobs.htm');

    }

    function get($f3, $args) { 
      //print_r($args);
      $this->job->load(array('Job=?', $args['item']));
      if ($this->job->dry())
        echo 'No record matching criteria';

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
      echo Template::instance()->render('views/job.htm');
    }
    function post() {
    }
    function put() {
    }
    function delete() {
    }
}


?>
