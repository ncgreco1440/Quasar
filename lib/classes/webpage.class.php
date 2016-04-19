<?php
class Webpage 
{
    private $_pageName;

    public function __construct($page = "Default") 
    {
        echo "Instance of Webpage class constructed! <br/>";
        $this->_pageName = $page;
    }

    public function setPageName($name)
    {
        $this->_pageName = $name;
    }

    public function getPageName($name)
    {
        return $this->_pageName;
    }
}