<?php
/*** AUTOLOAD ***/
require "../lib/autoload.php";

use Routes\Routes;
use Functions\Functions;

$route = Routes::getRoute();
$copyright = Functions::yieldCopyright();

/*** VIEWS ***/
require "../resources/views/app.page.php";

