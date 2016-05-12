<?php
use Quasar\Kernel;
use Authentication\Authenticate;
use Authentication\Validate;
use Quasar\Page;

class AdminHome extends Page
{
    public function load()
    {
        $query = Kernel::getQuery() != "" ? Kernel::getQuery() : false;
        switch($query)
        {
            case "signin": {
                $scope = "nestedScope_signin";
                return self::$scope();
            }
            case "forgot-password": {
                $scope = "nestedScope_forgotpassword";
                return self::$scope();
            }
        }

        Authenticate::loggedIn();
        $message = self::analyzePost();     // Analyze Post


        $page = "Quasar";

        return ["file" => "admin/home", "query" => $query, "content" => compact("page", "message")];
    }

    /**
     * [nestedScope_signin]
     *
     * Nested scope within admin_home, if the user is not signed in, they will be force redirected
     * to this page instead. This applies for all pages.
     *
     * @return [array] [information to be dealt with on the view]
     */
    private function nestedScope_signin()
    {
        $query = "signin";
        $message = false;

        if(isset($_REQUEST['passwordReset']))
            $message = Authenticate::resetPass($_POST['email']);

        if(isset($_REQUEST['login']))
            $message = Authenticate::logIn($_POST['username'], $_POST['password'], "/admin/home");

        return ["file" => "admin/signin", "query" => $query,
            "content" => compact("page", "message")];
    }

    /**
     * [nestedScope_forgotpassword]
     *
     * Nested scope within admin_home. If the user has forgotten their password, they can navigate
     * manually to this page and submit the reset-password form there.
     *
     * @return [array] [information to be dealt with on the view]
     */
    private function nestedScope_forgotpassword()
    {
        $query = "forgot-password";
        return ["file" => "admin/reset-password", "query" => $query,
            "content" => compact("page")];
    }

    /**
     * [analyzePost Will take a look at the post a determine
     * if it's a standard contact or signin form]
     * @param  [array] $post [posted values from $_POST]
     * @return [void]       [void]
     */
    private function analyzePost()
    {
        if(isset($_REQUEST['logout']))
            Authenticate::logOut();
    }
}