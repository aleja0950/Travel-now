<?php
class homecontroller
{
    public function index()
    {
        // Load a public home page
        require_once __DIR__ . "/../views/home/index.php";
    }
}