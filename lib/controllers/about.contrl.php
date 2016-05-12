<?php
use Quasar\Kernel;
use Authentication\Authenticate;
use Authentication\Validate;
use Quasar\Page;

class About extends Page
{
    public function load()
    {
        $query = Kernel::getQuery() != "" ? Kernel::getQuery() : false;
        $page = "About";
        return ["file" => "pages/about", "query" => $query, "content" => compact("page")];
    }
}