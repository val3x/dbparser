<?php
namespace  App\Repository;
class Error
{
    public static $instance;
    private $errors = [];


    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new Error();
        }

        return static::$instance;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getLastError()
    {
        return end($this->errors);
    }


    public function clearErrors()
    {
        $this->errors = [];
        return $this;
    }

    public function addError($error)
    {
        $this->errors[] = $error;
        return $this;
    }

    public function errorsCount()
    {
        return count($this->errors);
    }

    public function hasErrors()
    {
        return $this->errorsCount() > 0;
    }
    
}