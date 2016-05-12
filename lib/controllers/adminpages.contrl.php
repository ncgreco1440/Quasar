<?php
use Quasar\Kernel;
use Authentication\Authenticate;
use Authentication\Validate;
use Quasar\Page;
use Database\Connection;

class AdminPages extends Page
{
    public function load()
    {
        Authenticate::loggedIn();
        $message = self::analyzePost();

        $selectedPage = false;
        $query = Kernel::getQuery() != "" ? Kernel::getQuery() : false;
        if($query)
            $selectedPage = self::getWebPage($query);

        $page = "Quasar | Pages";
        $text = "Manage your websites webpages.";

        $webpages = self::getWebPages();
        return ["file" => "admin/pages", "query" => $query,
            "content" => compact("page", "text", "webpages", "selectedPage")];
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

    private function getWebPage($page)
    {
        return ["Pagename" => $page, "Text" => $this->getWebPageText($page),
            "Images" => $this->getWebPageImg($page)];
    }

    private function getWebPages()
    {
        $select = "*";
        $from = "main_navigation";
        $webpages = Connection::simplySelAll(compact("select", "from"));
        foreach($webpages as $key => $value)
        {
            $tmp = strtolower($value['name']);
            $tmp = str_replace(" ", "-", $tmp);
            $value['name'] == "Home" ? $value['link'] = "/" : $value['link'] = $tmp;
            $webpages[$key] = $value;
        }
        return $webpages;
    }

    private function getWebPageText($page)
    {
        $select = "*";
        $from = strtolower($page)."_content_txt";
        $text = Connection::simplySelAll(compact("select", "from"));
        $return = [];
        foreach($text as $key => $value)
            array_push($return, $value['paragraph']);
        return $return;
    }

    private function setWebPageText($page)
    {

    }

    private function getWebPageImg($page)
    {

    }

    private function setWebPageImg($page)
    {

    }
}