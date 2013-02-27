<?php

$streamCount = 10;
$streams     = array();
foreach (range(0, $streamCount - 1) as $i) {
    //connect to server
    $stream = stream_socket_client('tcp://127.0.0.1:80');
    //make stream non-blocking.
    stream_set_blocking($stream, 0);
    $streams[(int) $stream] = $stream;
}

$microtime = microtime(true);
foreach ($streams as $stream) {
    //init http request
    fwrite($stream, "GET /random-sleep.php HTTP/1.0\r\nHost: 127.0.0.1\r\nAccept: */*\r\n\r\n");
}

echo "Start polling streams...\n\n";

//the duration in which we will block for stream activity to happen
$activityInterval = 200000;
while (0 < count($streams)) {
    //assign remaining open streams to be read
    $reads = $streams;

    //filter $reads array to just streams that had any activity in
    //this blocks whilst it checks for activity
    if (stream_select($reads, $writes = null, $except = null, 0, $activityInterval) > 0) {

        //handle streams that had any activity
        foreach ($reads as $stream) {

            //we only care that stream had activity so we'll close and unset them
            fclose($streams[(int) $stream]);
            unset($streams[(int) $stream]);

            echo 'Stream ' . (int) $stream . " Completed in " . number_format((float) (microtime(true) - $microtime), 3) . " seconds \n";
        }
    }
}

