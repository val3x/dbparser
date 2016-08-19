<?php
namespace App\Repository;

class IO {
    protected $uploadFolder;
    protected $sqlFolder;
    protected $xmlFolder;

    public function __construct()
    {
        $this->uploadFolder = __DIR__."/../../upload";
        $this->sqlFolder = $this->uploadFolder."/sql";
        $this->xmlFolder = $this->uploadFolder."/xml";
    }

    public function getFolderItems($dir){
        $arDir = scandir($dir);
        $arFile = [];
        foreach ($arDir as $file) {
            if($file !== "." && $file !== ".."){
               $arFile[] = $file;
            }
        }
        return $arFile;
    }
    
    public function getSqlFiles(){
        return $this->getFolderItems($this->sqlFolder);
    }

    public function getSqlFolder(){
        return $this->sqlFolder;
    }

    public function getXmlFolder(){
        return $this->xmlFolder;
    }
}