<?php

// F3 init
$f3 = require_once('lib/base.php');
$f3->set('AUTOLOAD', __dir__ . ';classes/;lib/');

// read config settings, with defaults
$f3->config('default.ini');
if (file_exists('config.ini'))
  $f3->config('config.ini');
$debug = $f3->get('debug');

#$job=new DB\SQL\Mapper($db,'jobcard');
#$job->load(array('jcard_no=?','3'));
#if ($job->dry())
#    echo 'No record matching criteria';
#$job->cost_note = 'this is expensive!';
#$job->save();    // write a value to the DB

// show the page
$f3->run();

?>
