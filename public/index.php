<?php
/*** AUTOLOAD ***/
require "../lib/autoload.php";

use Routes\Routes;

$route = Routes::getRoute();

$calling_page = new $route;

$view = $calling_page->load();

extract($view);
extract($content);

/*** VIEWS ***/
if($query == "signin")
    require "../resources/views/admin/signin.page.php";
else if($query == "forgot-password")
    require "../resources/views/admin/forgot-password.page.php";
else
    require "../resources/views/app.page.php";



