<?php

// F3 init
// assume F3 is in this repo
$f3 = require_once('lib/base.php');
$f3->set('AUTOLOAD', __dir__ . ';classes/;lib/');

// read config settings, with defaults
$f3->config('default.ini');
if (file_exists('config.ini'))
  $f3->config('config.ini');
$debug = $f3->get('debug');
$f3->set('LOGS', '/tmp/');  //default is ./
$logger = new \Log($f3->get('logfile'));
\Registry::set('logger', $logger);

# connect to database
$db=new DB\SQL(sprintf("mysql:host=%s;port=3306;dbname=%s", $f3->get('dbhost'),  $f3->get('dbname')),
  $f3->get('dbuser'), $f3->get('dbpw') );
  \Registry::set('db', $db);


// <f3 init> done -------------------

if ($debug == 3) {
  $logger->write('index.php');
}

#$job=new DB\SQL\Mapper($db,'jobcard');
#$job->load(array('jcard_no=?','3'));
#if ($job->dry())
#    echo 'No record matching criteria';
#$job->cost_note = 'this is expensive!';
#$job->save();    // write a value to the DB

// note: most routes are set in the config.ini


// show the page
$f3->run();

// finish up
#if ($debug == 3) {
  #echo $db->log();
  #echo '<br>' 
  #$logger->write(\Registry::get('db')->log());
  #$execution_time = round(microtime(true) - $f3->get('TIME'), 3);
  #$logger->write('Executed in ' . $execution_time . ' secs using ' . round(memory_get_usage() / 1024 / 1024, 2) . '/' . round(memory_get_peak_usage() / 1024 / 1024, 2) . ' MB memory/peak');
#}

?>
