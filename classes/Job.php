<?php

/*
 * This class abtracts the Job data model, but is also
 * a controller for controlling program flow
 * Should be they separated and how?
 *
 * Non Job specific controls and models are inherited.
 *
 */
class Job extends BaseDB {
  protected $job, $tpl;
  protected $table='v_jprint';

  public function __construct() {
    parent::__construct();
    $this->job=new DB\SQL\Mapper($this->db, $this->table);
  }

  /* 
   * menu / nagigation 
   */
  function about($f3, $args) {  
    $this->f3->set('message', 'Todo: General: sales rep, price/unit+kgs. Delivey:pallet params, product approved by.');
    $this->tpl = 'views/root.htm';
  }

  function root($f3, $args) {   // handle /
    $params=$this->f3->get('GET');
    #echo var_dump($params);
    #print_r($args);

    if (!isset($args['item'])) {     // no item after root path
      if (isset($params['list']) ) { // show specified report type
        $this->getAll($params['list']);

      } else {
        #echo "no item: show all";
        #$this->job->getAll();
        $this->f3->set('message', 'Welcome to the Job System');
        $this->tpl = 'views/root.htm';
      }

    } else {   // just one item
      #echo "index: one item " . $args['item'] . '<br/>';
      #$this->job->get('Spec');
      if ($this->get($f3, $args)) {
        $this->tpl = 'views/job.htm';
      } else {
        $this->f3->set('warn', 'Could not find job ' . $args['item']);
        $this->tpl = 'views/jobsearch.htm';
      };
    }
  }

  function get($f3, $args) {   // show one job
    $params=$this->f3->get('GET');
    //print_r($args);
    #echo var_dump($params);

    $this->sqlGetMats();   // virtual fields
    // pull the dataset
    $this->job->load(array('Job=?', $args['item']));
    if ($this->job->dry()) {  // could not find it
      $this->f3->set('warn', 'Could not find job ' . $args['item']);
      $this->tpl = 'views/jobsearch.htm';
      return;
    }

    $f3->set('CustPath', $this->getCustShortcut());
    $f3->set('job', $this->job);
    if (isset($params['alt']) && ($params['alt'] == 'json') ) { // json
      header('Content-Type: application/json');    // probably too late?
      echo json_encode($this->job->cast());

    } else {     // normal html
      $this->tpl = 'views/job.htm';
    }
  }



  function find() {
    // which job nr.?
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
    $this->log('in Job::found() got:' . $find);
    $this->f3->reroute("/job/$find");   // show that job
  }


  /* data models */

  /* grab a list of jobs */
  function getall($list='') {
    $limit=$this->f3->get('joblistlimit');
    // todo: limit=25&offset=50
    switch ($list) {
      case 'jobex':
        $sql = "select prodex, prodsex as Seq, Job, PrinterLookup as Machine, Print_ref, Customer, JobStatus, Del_date1, Extrusion, Printing, Slitting, Lamination, Conversion from v_jprint where Extrusion='Y' order by prodex DESC limit $limit";
        $this->f3->set('reporttitle', "Next pending $limit Extrusion jobs");
        break;
      case 'jobpr':
      default:
        $this->f3->set('reporttitle', "Next pending $limit Print jobs");
        $sql = "select prodpr, prodspr as Seq, Job, PrinterLookup as Machine, Print_ref, Customer, JobStatus, Del_date1, Extrusion, Printing, Slitting, Lamination, Conversion from v_jprint where Printing='Y' order by prodpr DESC limit $limit";
        break;
    }
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
    #$this->job->Ex_l2_m5_dens = $this->sqlGetMat('Ex_l2_m5_type', 'Ex_l2_m5', 'density');
    #$this->job->Ex_l2_m6_dens = $this->sqlGetMat('Ex_l2_m6_type', 'Ex_l2_m6', 'density');
    $this->job->Ex_l2_m1_mat  = $this->sqlGetMat('Ex_l2_m1_type', 'Ex_l2_m1');
    $this->job->Ex_l2_m2_mat  = $this->sqlGetMat('Ex_l2_m2_type', 'Ex_l2_m2');
    $this->job->Ex_l2_m3_mat  = $this->sqlGetMat('Ex_l2_m3_type', 'Ex_l2_m3');
    $this->job->Ex_l2_m4_mat  = $this->sqlGetMat('Ex_l2_m4_type', 'Ex_l2_m4');
    #$this->job->Ex_l2_m5_mat  = $this->sqlGetMat('Ex_l2_m5_type', 'Ex_l2_m5');
    #$this->job->Ex_l2_m6_mat  = $this->sqlGetMat('Ex_l2_m6_type', 'Ex_l2_m6');

    $this->job->Ex_l3_m1_dens = $this->sqlGetMat('Ex_l3_m1_type', 'Ex_l3_m1', 'density');
    $this->job->Ex_l3_m2_dens = $this->sqlGetMat('Ex_l3_m2_type', 'Ex_l3_m2', 'density');
    $this->job->Ex_l3_m3_dens = $this->sqlGetMat('Ex_l3_m3_type', 'Ex_l3_m3', 'density');
    $this->job->Ex_l3_m4_dens = $this->sqlGetMat('Ex_l3_m4_type', 'Ex_l3_m4', 'density');
    $this->job->Ex_l3_m5_dens = $this->sqlGetMat('Ex_l3_m5_type', 'Ex_l3_m5', 'density');
    $this->job->Ex_l3_m6_dens = $this->sqlGetMat('Ex_l3_m6_type', 'Ex_l3_m6', 'density');
    $this->job->Ex_l3_m1_mat  = $this->sqlGetMat('Ex_l3_m1_type', 'Ex_l3_m1');
    $this->job->Ex_l3_m2_mat  = $this->sqlGetMat('Ex_l3_m2_type', 'Ex_l3_m2');
    $this->job->Ex_l3_m3_mat  = $this->sqlGetMat('Ex_l3_m3_type', 'Ex_l3_m3');
    $this->job->Ex_l3_m4_mat  = $this->sqlGetMat('Ex_l3_m4_type', 'Ex_l3_m4');
    $this->job->Ex_l3_m5_mat  = $this->sqlGetMat('Ex_l3_m5_type', 'Ex_l3_m5');
    $this->job->Ex_l3_m6_mat  = $this->sqlGetMat('Ex_l3_m6_type', 'Ex_l3_m6');
    // cannot create virtual fields that depend on virtual fields it seems:
    // Unknown column 'Ex_l1_m1_dens' in 'field list'
    //$this->job->Ex_l1_dens = 'Ex_l1_m1_per*Ex_l1_m1_dens/100 + Ex_l1_m2_per*Ex_l1_m2_dens/100';
    //$this->job->Ex_av_dens =
  }

  /*
   * create a unique path for customer specs (inherited from the Delphi function)
   */
  function getCustShortcut() {
    #$cust = " Test./( - cUst+ \' l\"td";
    $cust = $this->job->Customer;
    // strip all punctuation
    $cust = preg_replace("/(\/|,|-|\.|\(|\\\|\)|\'|\"|\s)/", '', strtolower(trim($cust)));
    $cust = substr($cust, 0, 7);  // first 7 chars

    $custcode = preg_replace("/(\/|,|-|\.|\(|\\\|\)|\'|\"|\s)/", '', strtolower(trim($this->job->Cust_code)));
    #$custcode = "$cust-$custcode";
    #$this->logger->write("code=$custcode cust=$cust");
    #return "$cust-$custcode";
    return "$custcode-$cust";
  }



  // Test: Json output
  // http://192.168.10.128/jobcard/jobjson/77266
  // this should not be seperate but just give back json or a view depending on the Content-Type
  // application/json
  function getJson($f3, $args) {   // show one job
    // pull the dataset
    $this->sqlGetMats();
    $this->job->load(array('Job=?', $args['item']));
    if ($this->job->dry()) {  // could not find it
      $this->f3->set('warn', 'Could not find job ' . $args['item']);
      $this->tpl = 'views/jobsearch.htm';
      return;
    }
    $f3->set('CustPath', $this->getCustShortcut());
    $f3->set('job', $this->job);
    #$this->tpl = 'views/job.htm';
    // output in json format
    #print_r($this->job->cast());  // see cast() in mapper.php
    header('Content-Type: application/json');    // probably too late?
    echo json_encode($this->job->cast());
  }

  function next($f3, $args) {
    #print_r($this->job->Spec);
    #$this->job->next();
    #$f3->set('CustPath', $this->getCustShortcut());
    #$f3->set('job', $this->job);
    #$this->tpl = 'views/job.htm';
  }

  function post() {
  }
  function put() {
  }
  function delete() {
  }
}


?>
