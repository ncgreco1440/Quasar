<?php
namespace Routes;

use Quasar\Kernel;

class Routes
{
/** ====================================================================================

    getRoute will get the route specificed within the URL, and then pair it with
    a controller within page.contrl.php after it has analyzed any queries
    that proceed the actual URI. Queries are handled by the routes'
    controller.

====================================================================================*/
    public static function getRoute()
    {
        $request = Kernel::getRequest();
        $request = self::analyzeRequest($request);
        switch($request)
        {
            case "/": {
                return "home";
            }
            case "/about": {
                return "about";
            }
            case "/contact": {
                return "contact";
            }
            case "/admin": {
                header("Location: /admin/home");
            }
            case "/admin/home": {
                return "adminhome";
            }
            case "/admin/pages": {
                return "adminpages";
            }
            case "/admin/users": {
                return "adminusers";
            }
            case "/admin/help": {
                return "adminhelp";
            }
            default: {
                // Went to invalid route, send to home page
                header("Location: /");
            }
        }
    }

    public static function prepareView()
    {
        //TODO
    }

    private static function analyzeRequest($request)
    {
        if(Kernel::getQuery() == "")
            return $request;
        else
            return substr($request, 0, strpos($request, "?"));
    }
}