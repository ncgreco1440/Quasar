<?php
class Website
{
    private $_webSite;

    public function __construct($website = "Default") 
    {
        echo "Instance of Website class constructed! <br/>";
        $_webSite = $website;
    }

    public function getWebsite()
    {
        return $this->$_webSite;
    }
}