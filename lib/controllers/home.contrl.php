<?php
use Quasar\Kernel;
use Authentication\Authenticate;
use Authentication\Validate;
use Quasar\Users;
use Quasar\Page;

class Home extends Page
{
    public function load()
    {
        $query = Kernel::getQuery() != "" ? Kernel::getQuery() : false;
        $page = "Home";
        return ["file" => "pages/home", "query" => $query,  "content" => compact("page")];
    }
}