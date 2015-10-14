<?php

require_once __DIR__ . '/bootstrap.php';

$loop = \React\EventLoop\Factory::create();

$client = new PgAsync\Client('file:/tmp/.s.PGSQL.5432', $loop);

$client->connect([
    "user"     => "matt",
    "database" => "matt"
]);

$jsonObserverFactory = function () {
    return new \Rx\Observer\CallbackObserver(
        function ($row) {
            echo json_encode($row) . "\n";
        },
        function ($err) {
            echo "ERROR: " . json_encode($err) . "\n";
        },
        function () {
            echo "Complete.";
        }
    );
};

$statement = $client->executeStatement("SELECT * FROM channel WHERE id = $1", ['2']);

$statement
    ->subscribe($jsonObserverFactory());

$insertStatement = $client->executeStatement("InSErT INTO channel(name, description) VALUES($1, $2)", ['The name', null]);

$insertStatement
    ->subscribe($jsonObserverFactory());

$statement
    ->subscribe($jsonObserverFactory());

$loop->run();