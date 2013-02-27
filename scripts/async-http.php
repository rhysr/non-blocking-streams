<?php

$streamCount = 10;
$streams     = array();
foreach (range(0, $streamCount - 1) as $i) {
    //connect to server
    $streams[$i] = stream_socket_client('tcp://127.0.0.1:80');
    //make stream non-blocking.
    stream_set_blocking($streams[$i], 0);
}

foreach ($streams as $stream) {
    //init http request
    fwrite($stream, "GET /random-sleep.php HTTP/1.0\r\nHost: 127.0.0.1\r\nAccept: */*\r\n\r\n");
}

echo "Start polling streams...\n\n";
$successOrder = array();
while (true) {
    foreach ($streams as $i => $stream) {
        echo "Stream $i...";

        //get any data pending in stream
        //will return immediately as it's non-blocking
        //blocking socket would wait until there was a response
        $data = fgets($stream);
        if ($data) {
            $successOrder[$i] = time();

            //tidy up stream
            fclose($streams[$i]);
            unset($streams[$i]);

            echo "success\n";
            continue;
        }
        //slow down rate of polling so logs are readable
        usleep(200000);
        echo "\n";
    }

    if (empty($streams)) {
        echo "All streams finished\n\n";
        break;
    }
}

foreach ($successOrder as $streamId => $time) {
    echo "Stream $streamId finished at " . date('H:i:s', $time)  . "\n";
}

