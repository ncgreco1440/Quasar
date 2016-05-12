<?php
use Quasar\Kernel;
use Authentication\Authenticate;
use Authentication\Validate;
use Quasar\Page;

class AdminHelp extends Page
{
    public function load()
    {
        Authenticate::loggedIn();
        $message = self::analyzePost();

        $query = Kernel::getQuery() != "" ? Kernel::getQuery() : false;

        $page = "Quasar | Help";
        return ["file" => "admin/help", "query" => $query, "content" => compact("page")];
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