<?php
/*** AUTOLOAD ***/
require "../lib/autoload.php";

use Routes\Routes;

$route = Routes::getRoute();

$this_page = new Page;

$view = $this_page->$route();

extract($view);
extract($content);

/*** VIEWS ***/
if($query == "signin")
    require "../resources/views/admin/signin.page.php";
else if($query == "forgot-password")
    require "../resources/views/admin/forgot-password.page.php";
else
    require "../resources/views/app.page.php";



