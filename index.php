<?php
// Just a single include statement!
include "lib/autoload.php";
use Database;

echo "Hello, World!<br/><br/>";

$web = new Website("Leverage");
$webpage = new Webpage("A New Web Page");
Database\Connection::connect("localhost", "DB_LEVERAGE", "client", "client");
Database\Extra::invoked();

