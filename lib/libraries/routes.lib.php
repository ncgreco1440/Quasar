<?php
namespace Routes;

use Quasar\Kernel;

class Routes
{
    public static function getRoute()
    {
        switch(Kernel::getRequest())
        {
            case "/": {
                return "home";
            }
            case "/about": {
                return "about";
            }
        }
    }
}