<?php
// Define the time limit in seconds (5 minutes = 300 seconds)
$timeLimit = 300;

// Function to get a date/time stamp
function logMessage($message) {
    $timestamp = date("Y-m-d H:i:s");
    echo "[$timestamp] $message\n";
}

// Execute the shell command to get all lsphp processes
$output = [];
exec("ps -eo user,pid,etime,cmd | grep lsphp | grep -v grep", $output);

// Array to store PIDs that were killed
$killedPIDs = [];

// Loop through the processes
foreach ($output as $line) {
    // Parse the process details
    preg_match('/^(\S+)\s+(\d+)\s+([\d\-:]+)\s+(.*)$/', $line, $matches);
    if (count($matches) < 5) {
        continue;
    }

    $user = $matches[1];
    $pid = $matches[2];
    $etime = $matches[3];
    $cmd = $matches[4];

    // Convert elapsed time to seconds
    $elapsedTime = 0;
    if (preg_match('/^(\d+)-(\d+):(\d+):(\d+)$/', $etime, $timeParts)) {
        $elapsedTime = ($timeParts[1] * 86400) + ($timeParts[2] * 3600) + ($timeParts[3] * 60) + $timeParts[4];
    } elseif (preg_match('/^(\d+):(\d+):(\d+)$/', $etime, $timeParts)) {
        $elapsedTime = ($timeParts[1] * 3600) + ($timeParts[2] * 60) + $timeParts[3];
    } elseif (preg_match('/^(\d+):(\d+)$/', $etime, $timeParts)) {
        $elapsedTime = ($timeParts[1] * 60) + $timeParts[2];
    }

    // Kill the process if it exceeds the time limit
    if ($elapsedTime > $timeLimit) {
        logMessage("Killing process owned by $user - PID: $pid, Elapsed Time: $etime, Command: $cmd");
        exec("kill $pid");
        $killedPIDs[] = $pid; // Save the PID for further check
    }
}

// Wait 5 seconds before ensuring the processes are killed
if (!empty($killedPIDs)) {
  //  logMessage("Waiting 5 seconds before force killing processes...");
    sleep(5);
    foreach ($killedPIDs as $pid) {
        // Check if the process is still running
        exec("ps -p $pid", $checkOutput, $status);
        if ($status === 0) { // If the process is still running
            logMessage("Force killing process - PID: $pid");
            exec("kill -9 $pid");
        } else {
//            logMessage("Process already terminated - PID: $pid");
        }
    }
}
