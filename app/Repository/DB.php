<?php
namespace App\Repository;

class DB extends Main
{

    public $host;
    public $dbname;
    public $user;
    public $pass;
    public $error;


    /**
     * DB constructor.
     * @param $host
     * @param $dbname
     * @param $user
     * @param $pass
     */
    public function __construct($host, $dbname, $user, $pass)
    {
        parent::__construct();
        $this->host = $host;
        $this->dbname = $dbname;
        $this->user = $user;
        $this->pass = $pass;
    }


    public function connect()
    {
        try {
            return new \PDO("mysql:host=$this->host;dbname=$this->dbname;charset=utf8", $this->user, $this->pass, array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        } catch (\PDOException $e) {
            $this->error->addError($e->getMessage());
        }
    }


}