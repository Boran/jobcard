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

  function sqlGetMat($type, $mat, $value='value') {
    #$this->job->Ex_l1_m1_dens = '(case when Ex_l1_m1_type in (1) then (select density from e_gran where Code=Ex_l1_m1)
    #    when Ex_l1_m1_type in (2) then (select density from e_colour where Code=Ex_l1_m1)
    #    when Ex_l1_m1_type in (3) then (select density from e_addit where Code=Ex_l1_m1)
    #    when Ex_l1_m1_type in (4) then (select density from e_hd where Code=Ex_l1_m1)
    #    when Ex_l1_m1_type in (5) then (select density from e_mett where Code=Ex_l1_m1) end) ';
    return "(case when $type in (1) then (select $value from e_gran where Code=$mat)
                  when $type in (2) then (select $value from e_colour where Code=$mat)
                  when $type in (3) then (select $value from e_addit where Code=$mat)
                  when $type in (4) then (select $value from e_hd where Code=$mat)
                  when $type in (5) then (select $value from e_mett where Code=$mat)
    end) ";
  }
  function sqlGetMats() {
    // virtual fields: density per layer and average
    $this->job->Ex_l1_m1_dens = $this->sqlGetMat('Ex_l1_m1_type', 'Ex_l1_m1', 'density');
    $this->job->Ex_l1_m2_dens = $this->sqlGetMat('Ex_l1_m2_type', 'Ex_l1_m2', 'density');
    $this->job->Ex_l1_m3_dens = $this->sqlGetMat('Ex_l1_m3_type', 'Ex_l1_m3', 'density');
    $this->job->Ex_l1_m4_dens = $this->sqlGetMat('Ex_l1_m4_type', 'Ex_l1_m4', 'density');
    $this->job->Ex_l1_m5_dens = $this->sqlGetMat('Ex_l1_m5_type', 'Ex_l1_m5', 'density');
    $this->job->Ex_l1_m6_dens = $this->sqlGetMat('Ex_l1_m6_type', 'Ex_l1_m6', 'density');
    $this->job->Ex_l1_m1_mat  = $this->sqlGetMat('Ex_l1_m1_type', 'Ex_l1_m1');
    $this->job->Ex_l1_m2_mat  = $this->sqlGetMat('Ex_l1_m2_type', 'Ex_l1_m2');
    $this->job->Ex_l1_m3_mat  = $this->sqlGetMat('Ex_l1_m3_type', 'Ex_l1_m3');
    $this->job->Ex_l1_m4_mat  = $this->sqlGetMat('Ex_l1_m4_type', 'Ex_l1_m4');
    $this->job->Ex_l1_m5_mat  = $this->sqlGetMat('Ex_l1_m5_type', 'Ex_l1_m5');
    $this->job->Ex_l1_m6_mat  = $this->sqlGetMat('Ex_l1_m6_type', 'Ex_l1_m6');

    $this->job->Ex_l2_m1_dens = $this->sqlGetMat('Ex_l2_m1_type', 'Ex_l2_m1', 'density');
    $this->job->Ex_l2_m2_dens = $this->sqlGetMat('Ex_l2_m2_type', 'Ex_l2_m2', 'density');
    $this->job->Ex_l2_m3_dens = $this->sqlGetMat('Ex_l2_m3_type', 'Ex_l2_m3', 'density');
    $this->job->Ex_l2_m4_dens = $this->sqlGetMat('Ex_l2_m4_type', 'Ex_l2_m4', 'density');
    $this->job->Ex_l2_m5_dens = $this->sqlGetMat('Ex_l2_m5_type', 'Ex_l2_m5', 'density');
    $this->job->Ex_l2_m6_dens = $this->sqlGetMat('Ex_l2_m6_type', 'Ex_l2_m6', 'density');
    $this->job->Ex_l2_m1_mat  = $this->sqlGetMat('Ex_l2_m1_type', 'Ex_l2_m1');
    $this->job->Ex_l2_m2_mat  = $this->sqlGetMat('Ex_l2_m2_type', 'Ex_l2_m2');
    $this->job->Ex_l2_m3_mat  = $this->sqlGetMat('Ex_l2_m3_type', 'Ex_l2_m3');
    $this->job->Ex_l2_m4_mat  = $this->sqlGetMat('Ex_l2_m4_type', 'Ex_l2_m4');
    $this->job->Ex_l2_m5_mat  = $this->sqlGetMat('Ex_l2_m5_type', 'Ex_l2_m5');
    $this->job->Ex_l2_m6_mat  = $this->sqlGetMat('Ex_l2_m6_type', 'Ex_l2_m6');

  }

  function get($f3, $args) {   // show one job
    //print_r($args);
    $this->sqlGetMats();
    //$this->job->Ex_l1_dens = 'Ex_l1_m1_per*Ex_l1_m1_dens/100 + Ex_l1_m2_per*Ex_l1_m2_dens/100';
    //$this->job->Ex_av_dens =
    // pull the dataset
    $this->job->load(array('Job=?', $args['item']));
    if ($this->job->dry()) {  // could not find it
      $this->f3->set('warn', 'Could not find job ' . $args['item']);
      $this->tpl = 'views/jobsearch.htm';
      return;
    }

    #$result = $this->db->exec('SELECT density FROM e_gran where Code="' . $this->job->Ex_l1_m1 .'"');
    #$result = $this->db->exec("SELECT density FROM e_gran where Code=1");
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
