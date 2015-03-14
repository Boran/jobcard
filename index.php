<?php


// F3 init
// assume F3 is in a directory parallel to this one
// bootstrap too
$f3 = require_once('../fatfree-master/lib/base.php');
$f3->set('AUTOLOAD', __dir__ . ';classes/');

// read config settings, with defaults
$f3->config('default.ini');
if (file_exists('config.ini'))
  $f3->config('config.ini');
$debug = $f3->get('debug');

# connect to database
$db=new DB\SQL(sprintf("mysql:host=%s;port=3306;dbname=%s", $f3->get('dbhost'),  $f3->get('dbname')),
  $f3->get('dbuser'), $f3->get('dbpw') );
  \Registry::set('db', $db);

$logger = new \Log($f3->get('logfile'));
#$logger->write('called index.php');
\Registry::set('logger', $logger);

// </init> done -------------------


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
