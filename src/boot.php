#!/usr/bin/phpchmod +x
<?php

namespace App;

use App\classes\RightManagement;
use App\utilities\database\Client;

include 'includeClasses.php';

$env = parse_ini_file('.env');

$client = new Client(
        $env["DB_NAME"],
        $env["DB_HOST"],
        $env["DB_USER"],
        $env["DB_PASSWORD"]
);

$handle = fopen("php://stdin", "r");

echo "Please enter username: ";
$line = fgets($handle);
$username = trim($line);
$user = $client->findOne('users', ['username' => $username]);
if (!$user) {
    echo "user $username not found in database : (";
    return;
}

echo "Please enter function name: ";
$line = fgets($handle);
$functionName = trim($line);
$moduleFunction = $client->findOne('modulefunctions', ['name' => $functionName]);
if (!$moduleFunction) {
    echo "$functionName not found in system function list";
    return;
}

$rightManagement = new RightManagement($client);
$result = $rightManagement->checkRight($user, $functionName);


if ($result == true) {
    echo "User $username has access to $functionName \r\n";
} else {
    echo "Access denied for $username to $functionName";
}



