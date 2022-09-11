<?php

define('SIZE', 6);
define('UPPERLIMIT', 10 ** SIZE - 1);
define('LOWER', 'first');
define('UPPER', 'end');

require realpath('../src/helper.php');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send(405, 'Method not supported');
}

$first = getValue(LOWER);
$last = getValue(UPPER);

$messages = validation(LOWER, $first);
$messages .= validation(UPPER, $last);

if (!empty($messages)) {
    send(400, $messages);
}

require realpath('../src/LuckyTickets.php');

try {

    $tickets = new LuckyTickets(SIZE);

    $count = $tickets->count($first, $last);
    send(200, "Count of lucky tickets: " . $count);

} catch (\InvalidArgumentException $ex) {
    send(400, "'" . UPPER . "' value must be greater than '" . LOWER  ."' value");
} catch (\Throwable $ex) {
    send(500, 'Sorry, this may be an error on the server side');
}
