<?php
/*** HEADER ***/
if(strstr($file, "admin"))
    require "partials/quasar-header.partial.php";
else
    require "partials/header.partial.php";

/*** FILE ***/
require "$file.page.php";

/*** FOOTER ***/
if(strstr($file, "admin"))
    require "partials/quasar-footer.partial.php";
else
    require "partials/footer.partial.php";

