<?php
use Quasar\Kernel;
use Authentication\Authenticate;
use Authentication\Validate;
use Quasar\Users;
use Quasar\Page;

class AdminUsers extends Page
{
    public function load()
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