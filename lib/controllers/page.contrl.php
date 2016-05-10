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
    private $public_pages =
    [
        "home",
        "about",
        "contact"
    ];

    private $admin_pages =
    [
        "admin_home",
        "admin_users",
        "admin_pages",
        "admin_help"
    ];


    public function __call($method, $args)
    {
        return $this->$method();
    }
/* =================================================================================================
        PUBLIC PAGES
================================================================================================= */
    private function home()
    {
        $query = Kernel::getQuery() != "" ? Kernel::getQuery() : false;
        $page = "Home";
        return ["file" => "pages/home", "query" => $query,  "content" => compact("page")];
    }

    private function about()
    {
        $query = Kernel::getQuery() != "" ? Kernel::getQuery() : false;
        $page = "About";
        return ["file" => "pages/about", "query" => $query, "content" => compact("page")];
    }

    private function contact()
    {
        $query = Kernel::getQuery() != "" ? Kernel::getQuery() : false;
        $page = "Contact";
        return ["file" => "pages/contact", "query" => $query, "content" => compact("page")];
    }

/* =================================================================================================
        PRIVATE PAGES
================================================================================================= */
    private function admin_home()
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

    private function admin_pages()
    {
        Authenticate::loggedIn();
        $message = self::analyzePost();

        $query = Kernel::getQuery() != "" ? Kernel::getQuery() : false;

        $page = "Quasar | Pages";
        return ["file" => "admin/pages", "query" => $query, "content" => compact("page")];
    }

    private function admin_users()
    {
        Authenticate::loggedIn();
        $message = self::analyzePost();

        $user = false;
        $query = Kernel::getQuery() != "" ? Kernel::getQuery() : false;
        if($query)
            $user = self::getUser($query);


        $page = "Quasar | Users";
        $text = "This is the users page...";
        $users = self::getUsers();
        return ["file" => "admin/users", "query" => $query, "content" => compact("page", "text",
            "user", "users", "message")];
    }

    private function admin_help()
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
           return [];
    }

    private function getUsers()
    {
        if($token = Validate::validateToken())
            return Users::getUsers();
        else
            return [];
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
        if(isset($_REQUEST['contact'])) {}
            //return Mail::mailTo();
        if(isset($_REQUEST['chngPassword']))
        {
            if($_POST['newPass'] == $_POST['confPass'])
                return Authenticate::changePass($_POST['currPass'], $_POST['newPass']);
            else
                return ['success' => false, 'message' => "New and Confirm Passwords must match."];
        }
        if(isset($_REQUEST['userForm']))
        {
            if(Validate::validateAuth(0))
                return Users\Profile::saveProfile($_POST['firstname'], $_POST['lastname'], $_POST['email']);
            else
                return ["success" => false, "message" => "Insufficient Permissions"];
        }
        if(isset($_REQUEST['privledges']))
        {
            if(Validate::validateAuth(0))
                return Users\Profile::assignPrivledge($_POST['username'], $_POST['privledge']);
        }
    }
}