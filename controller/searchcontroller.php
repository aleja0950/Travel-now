<?php
require_once __DIR__ . "/../models/search.php";

class searchcontroller
{
    public function index()
    {
        $search = new search();
        $datos = $search->index();

        require_once __DIR__ . "/../views/search/index.php";
    }
}
