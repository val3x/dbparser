<?php
namespace App;

use App\Repository\Error;

class Bootstrap
{

    public $db;
    public $config;
    public $import;
    public $dbTemp;

    function __construct()
    {
        $this->__autoloadClasses();
        $this->error = Error::getInstance();
        $this->io = new Repository\IO();
        $this->dbTemp = (new Repository\DB(
            $this->config['dbTemp']['host'],
            $this->config['dbTemp']['dbname'],
            $this->config['dbTemp']['user'],
            $this->config['dbTemp']['password']
        ))->connect();
        $this->db = (new Repository\DB(
            $this->config['dbParser']['host'],
            $this->config['dbParser']['dbname'],
            $this->config['dbParser']['user'],
            $this->config['dbParser']['password']
        ))->connect();
        $this->import = new Repository\Import($this);
        $this->checkSystem();
        $this->saveFile();

    }


    protected function __autoloadClasses()
    {
        // позже как то, автоматически
        $this->config = require_once "config.php";
        require_once "Repository/Error.php";
        require_once "Repository/Main.php";
        require_once "Repository/DB.php";
        require_once "Repository/IO.php";
        require_once "Repository/Import.php";
        require_once "Repository/Xml.php";

    }

    public function getErrors(){
        return $this->error->getErrors();
    }

    public function checkSystem(){
        //php version
        $versionPhp = (float) substr(phpversion(), 0, 3);
        if($versionPhp < 5.4){
            $this->error->addError('Php ниже 5.4');
        }

    }

    public function getDBFiles()
    {
        $arFile = $this->io->getSqlFiles();
        $arFileSql = [];
        foreach ($arFile as $item) {
            $info = pathinfo($item);
            if ($info['extension'] === 'sql') {
                $arFileSql[] = $item;
            }
        }
        return $arFileSql;
    }

    public function getDBTemp(){
        return $this->dbTemp;
    }

    public function saveFile(){
        if(!empty($_FILES['f']["tmp_name"])){
            $uploaddir = $this->io->getSqlFolder()."/";
            foreach ($_FILES['f']["tmp_name"] as $key => $item) {
                $name = $_FILES['f']["name"][$key];
                $name = preg_replace( '/[^a-zA-Z0-9\.]/', '_', $name );
                $file_name = $uploaddir.$name;
                if (move_uploaded_file($item, $file_name)) {

                } else {
                    $this->error->addError('Ошибка загрузки файла '.$name);
                }
            }
        }
    }


}

function p($value)
{
    echo "<pre>";
    print_r($value);
    echo "</pre>";
}