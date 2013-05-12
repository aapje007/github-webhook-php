<?php
/*
 * hook.php
 * 
 * the file to process the webhook request.
 * (point to this file when configuring the webhook on the github website)
 */

include_once 'config.php';

// Make sure the configuration is setup
if (!isset($config) || empty($config)) {
        error_log('Missing config.php or no configuration definitions setup');
        exit;
}

// Function to append log messages to the log file
function logmsg($msg = ''){
    global $config;
    $handle = fopen($config['logfile'],'a');
    fwrite($handle,date("Y-m-d H:i:s").' - '.$msg.PHP_EOL);
    fclose($handle);
}


// Check for the GitHub WebHook Payload
if (!isset($_POST['payload'])) {
        logmsg("Missing expected POST parameter 'payload'");
        exit;
}
logmsg('Payload: '.$_POST['payload']);

// Decode the JSON payload from Github$
$payload = json_decode(stripslashes($_POST['payload']),true);
if ($payload === NULL) {logmsg ('Error parsing payload'); exit;}

// Define target branch(dir)
$branchname = substr(strrchr($payload['ref'], '/'), 1 );
$branchdir = '.branches/'.$branchname;

// Define the repository url
$repourl = (isset($config['repository_url'])? $config['repository_url'] : $payload['repository']['url']);

// Put shell commands into execute array
$execute = array();
$execute[] = 'whoami';
$execute[] = 'echo $PWD';
$execute[] = '[ -d '.escapeshellarg($branchdir).' ] && rm -rf '.escapeshellarg($branchdir).'/';
$execute[] = 'mkdir -p -m 777 '.escapeshellarg($branchdir);
$execute[] = 'git clone '.escapeshellarg($repourl).' '.escapeshellarg($branchdir).' -b '.escapeshellarg($branchname).' --single-branch --depth 0';

// Execute the commands
foreach($execute as $command){
 logmsg('executing command: '.$command);
 $out = exec($command.' 2>&1');
 if(strlen($out)>0) logmsg('output: '.$out);
}

?>
