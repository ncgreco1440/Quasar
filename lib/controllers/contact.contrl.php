<?php
use Quasar\Kernel;
use Authentication\Authenticate;
use Authentication\Validate;
use Quasar\Page;

class Contact extends Page
{
    public function load()
    {
        $query = Kernel::getQuery() != "" ? Kernel::getQuery() : false;
        $page = "Contact";
        return ["file" => "pages/contact", "query" => $query, "content" => compact("page")];
    }
}