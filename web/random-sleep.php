<?php

$sleep = filter_input(INPUT_GET, 'sleep', FILTER_VALIDATE_INT);
if (!$sleep) {
    $sleep = rand(1, 5);
}
sleep($sleep);
