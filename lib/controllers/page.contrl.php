<?php
/** ====================================================================================

    Backend controller for each page on the website. The controller is responsible
    for gathering and handling all the server side stored data and returning
    it to the front controller.

====================================================================================*/
use Quasar\Kernel;
use Authentication\Authenticate;
use Authentication\Validate;
use Quasar\Users;

class Page
{
/* =================================================================================================
        PUBLIC PAGES
================================================================================================= */
    public function home()
    {
        $query = Kernel::getQuery() != "" ? Kernel::getQuery() : false;
        $page = "Home";
        return ["file" => "pages/home", "query" => $query,  "content" => compact("page")];
    }

    public function about()
    {
        $query = Kernel::getQuery() != "" ? Kernel::getQuery() : false;
        $page = "About";
        return ["file" => "pages/about", "query" => $query, "content" => compact("page")];
    }

    public function contact()
    {
        $query = Kernel::getQuery() != "" ? Kernel::getQuery() : false;
        $page = "Contact";
        return ["file" => "pages/contact", "query" => $query, "content" => compact("page")];
    }

/* =================================================================================================
        PRIVATE PAGES
================================================================================================= */
    public function admin_home()
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

    public function admin_pages()
    {
        Authenticate::loggedIn();
        $message = self::analyzePost();

        $query = Kernel::getQuery() != "" ? Kernel::getQuery() : false;

        $page = "Quasar | Pages";
        return ["file" => "admin/pages", "query" => $query, "content" => compact("page")];
    }

    public function admin_users()
    {
        Authenticate::loggedIn();
        $message = self::analyzePost();

        $user = false;
        $query = Kernel::getQuery() != "" ? Kernel::getQuery() : false;
        if($query)
            $user = self::getUser($query);


        $page = "Quasar | Users";
        $text = "This is the users page...";
        $users = [["username" => "admin"], ["username" => "ncgreco"], ["username" => "jgonzalez"]];
        return ["file" => "admin/users", "query" => $query, "content" => compact("page", "text",
            "user", "users")];
    }

    public function admin_help()
    {
        Authenticate::loggedIn();
        $message = self::analyzePost();

        $query = Kernel::getQuery() != "" ? Kernel::getQuery() : false;

        $page = "Quasar | Help";
        return ["file" => "admin/help", "query" => $query, "content" => compact("page")];
    }

/* =================================================================================================
        PRIVATE METHODS
================================================================================================= */
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

    private function nestedScope_user()
    {
        //Get user info...
    }

    private function getUser($username)
    {
        if($token = Validate::validateToken())
            return Users::getUser($username);
        else
           return false;
    }

    /**
     * [analyzePost Will take a look at the post a determine
     * if it's a standard contact or signin form]
     * @param  [array] $post [posted values from $_POST]
     * @return [void]       [void]
     */
    private function analyzePost()
    {
        if(isset($_REQUEST['passwordReset']))
            return Authenticate::resetPass($_POST['email']);
        //if(isset($_REQUEST['login']))
            //return Authenticate::logIn($_POST['username'], $_POST['password'], "/admin/home");
        if(isset($_REQUEST['logout']))
            Authenticate::logOut();
        if(isset($_REQUEST['contact'])) {}
            //return Mail::mailTo();
    }
}