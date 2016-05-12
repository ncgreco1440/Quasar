<?php
namespace Quasar;
/** ====================================================================================

    Backend controller for each page on the website. The controller is responsible
    for gathering and handling all the server side stored data and returning
    it to the front controller.

====================================================================================*/
abstract class Page
{
    abstract public function load();
}