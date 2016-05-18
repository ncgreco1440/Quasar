<?php
use Quasar\Kernel;
use Authentication\Authenticate;
use Authentication\Validate;
use Quasar\Page;
use Database\Connection;
use Bulletproof\Image;

class AdminPages extends Page
{
    private $ID;

    public function load()
    {
        $query = Kernel::getQuery() != "" ? Kernel::getQuery() : false;
        $this->ID = Connection::query("SELECT `ID` FROM `Q_PAGES` WHERE `name` = '$query'", true)['ID'];
        Authenticate::loggedIn();
        $message = self::analyzePost();

        $selectedPage = false;

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
        if(isset($_POST['savePage']) && isset($_FILES))
        {
            $this->setWebPageText($_POST);
            $this->setWebPageImg($_FILES);
        }
    }

    private function getWebPage($page)
    {
        $contents = Connection::query("SELECT * FROM `Q_PAGES` WHERE `ID` = '$this->ID'", true);
        return ["Pagename" => $page, "Contents" => $contents];
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

    private function setWebPageText($post)
    {
        Connection::query("UPDATE `Q_PAGES` SET
            `main_text` = '$post[main_text]',
            `sub_text_1` = '$post[sub_text_1]',
            `sub_text_2` = '$post[sub_text_2]',
            `sub_text_3` = '$post[sub_text_3]'
            WHERE `ID` = $this->ID");
    }

    private function setWebPageImg($post)
    {
        $image = new Image($post);
        $image->setLocation(__DIR__."/../../public/images/uploads");
        $image->setSize(100, 1000000);
        $image->setDimension(2000, 2000);

        foreach($post as $key => $value)
        {
            if($image[$key])
            {
                $upload = $image->upload();
                if(!$upload)
                    echo $image["error"];
                $basis = $image->getFullPath();
                $imgType = strrev(substr(strrev($basis), 0, strpos(strrev($basis), ".") + 1));
                $result = Connection::query("UPDATE `Q_PAGES` SET
                    `$key` = "."'/images/uploads/".$image->getName().$imgType.
                    "' WHERE `ID` = '$this->ID'");
            }
        }

    }
}