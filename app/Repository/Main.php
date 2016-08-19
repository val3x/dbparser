<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 13.08.2016
 * Time: 21:49
 */

namespace App\Repository;


class Main
{
    public $error;

    public function __construct()
    {
        $this->error = Error::getInstance();
    } 
}