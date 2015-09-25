<?php
$url = 'http://www.youtube.com/watch?v=coq9klG41R8';
$template = '/var/www/html/thescene/' .
            'files/v/%(id)s.%(ext)s';
//$string = ('youtube-dl ' . escapeshellarg($url) . ' -f 18 -o ' . escapeshellarg($template)). ' &2>1';
$string = ('/usr/local/bin/youtube-dl https://www.youtube.com/watch?v=YFUF5OEUiD8' );

$descriptorspec = array(
   0 => array("pipe", "r"),  // stdin
   1 => array("pipe", "w"),  // stdout
   2 => array("pipe", "w"),  // stderr
);
$process = proc_open($string, $descriptorspec, $pipes);
$stdout = stream_get_contents($pipes[1]);
fclose($pipes[1]);
$stderr = stream_get_contents($pipes[2]);
fclose($pipes[2]);
$ret = proc_close($process);
echo '<pre>';
print_r(array('status' => $ret, 'errors' => $stderr,
                       'url_orginal'=>$url, 'output' => $stdout,
                       'command' => $string));


$cmd = 'whoami';
$output = shell_exec($cmd);
echo "<br>shell exec Command: ".$cmd." <pre>$output</pre>";
                       