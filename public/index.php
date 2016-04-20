<?php
/*** AUTOLOAD ***/
require "../lib/autoload.php";

use Functions\Functions;

$preView = Functions::prepareView();

$page = new Page;

$view = $page->$preView();
extract($view);             // split up the bundled array into $file and $content
extract($content);          // split up $content into n many variables this view needs

$copyright = Functions::yieldCopyright();

/*** VIEWS ***/
require "../resources/views/app.page.php";

