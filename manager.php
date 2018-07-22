<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$file = '/etc/ipsec.secrets';

function reload(){
	$output = shell_exec('sudo ipsec secrets');
    echo "<pre>$output</pre>";
}


if (isset($_REQUEST['adduser']) && !empty($_REQUEST['user']) && !empty($_REQUEST['password'])) {

    $user = $_REQUEST['user'];
    $password = $_REQUEST['password'];

    $line_pattern = "#{$user} : EAP \"(.*)\"\n#";

    $line = "{$user} : EAP \"{$password}\"\n";

    $contents = file_get_contents($file);
    $contents = preg_replace($line_pattern, '', $contents);
    $contents .= $line;
    file_put_contents($file, $contents, LOCK_EX);
	reload();
}
if (isset($_REQUEST['deluser']) && !empty($_REQUEST['user'])){
    $user = $_REQUEST['user'];
    $replace = "{$user} : EAP \"(.*)\"\n";
    $contents = file_get_contents($file);
    $line_pattern = "#{$user} : EAP \"(.*)\"\n#";
    $contents = preg_replace($line_pattern, '', $contents);
    file_put_contents($file, $contents, LOCK_EX);
	reload();
}
if (isset($_REQUEST['view'])) {
    echo file_get_contents($file);
}
if (isset($_REQUEST['reset'])) {
    $host = strtok($_SERVER['HTTP_HOST'], ':');
    $data = $host . ' : RSA "privkey.pem"' . "\n";
    file_put_contents($file, $data, LOCK_EX);
	reload();
}
if (isset($_REQUEST['ping'])) {
    echo 'pong';
}
if (isset($_REQUEST['status'])) {
    $output = shell_exec('sudo ipsec status | grep -c "ESTABLISHED"');
    echo $output;
}