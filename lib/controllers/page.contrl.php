<?php
class Page
{
    public function home()
    {
        $page = "Home";
        return ["file" => "pages/home", "content" => compact("page")];
    }

    public function about()
    {
        $page = "About";
        return ["file" => "pages/about", "content" => compact("page")];
    }

    public function contact()
    {
        $page = "Contact";
        return ["file" => "pages/contact", "content" => compact("page")];
    }
}