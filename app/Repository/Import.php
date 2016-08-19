<?php
namespace App\Repository;

class Import extends Main
{

    public $io;
    public $app;
    public $users;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function run($file)
    {
        if (empty($file)) return;

        $this ->runTempDB($file);
        if(empty($this->app->error->getErrors())){
            //$arUsers = $this->getUsers($file);
            $arPosts = $this->getPosts($file);
            //$arOptions = $this->getOptions($file);
            //$arCategory = $this->getCategory($file);
            // разделы
            /*foreach ($arCategory as $arItem) {
                unset($arItem['term_id']);
                $valuesName = implode(", ", array_keys($arItem));
                foreach ($arItem as &$item) {
                    $item = "'".$item."'";
                }
                unset($item);
                $values = implode(", ", $arItem);
                $CH = $this->app->db->prepare("INSERT INTO category ( $valuesName ) values ( $values )");
                $CH ->execute();
                unset($CH);

            }
            // пользователи
            foreach ($arUsers as $arItem) {
                unset($arItem['ID']);
                $valuesName = implode(", ", array_keys($arItem));
                foreach ($arItem as &$item) {
                    $item = "'".$item."'";
                }
                unset($item);
                $values = implode(", ", $arItem);
                $CH = $this->app->db->prepare("INSERT INTO users ( $valuesName ) values ( $values )");
                $CH ->execute();
                unset($CH);

            }
            // настройки
            foreach ($arOptions as $arItem) {
                unset($arItem['option_id']);
                $valuesName = implode(", ", array_keys($arItem));
                foreach ($arItem as &$item) {
                    $item = "'".$item."'";
                }
                unset($item);
                $values = implode(", ", $arItem);
                $CH = $this->app->db->prepare("INSERT INTO options ( $valuesName ) values ( $values )");
                $CH ->execute();
                unset($CH);

            }*/
            // посты
            foreach ($arPosts as $arItem) {
                unset($arItem['ID']);
                $valuesName = implode(", ", array_keys($arItem));
                foreach ($arItem as &$item) {
                    $item = str_replace("'", "", $item);
                    $item = "'".$item."'";
                }
                unset($item);
                $values = implode(", ", $arItem);
                $CH = $this->app->db->prepare("INSERT INTO posts ( $valuesName ) values ( $values )");
                $CH ->execute();
                unset($CH);

            }


        }else{
            //вывести ошибки для пользователя
            //\App\p($this->app->error->getErrors());
        }

        return $this;

    }

    public function runTempDB($file){
        //чистим базу
        $result = $this->app->dbTemp->query("SHOW TABLES");
        while ($row = $result->fetch(\PDO::FETCH_NUM)){
            $this->app->dbTemp->query("DROP TABLE $row[0]");
        }
        sleep(1);
        // загружаем базу
        $sql = $this->app->io->getSqlFolder() . "/" . $file;

        //очистим дамп от всякого хлама с созданием базы
        exec("sed -i \"/CREATE DATABASE/d\" \"$sql\"", $outt, $statust);
        exec("sed -i \"/USE/d\" \"$sql\"", $outt, $statust);

        $dbName = $this->app->config['dbTemp']['dbname'];
        $dbUser = $this->app->config['dbTemp']['user'];
        $dbPass = $this->app->config['dbTemp']['password'];

        $command = "mysql -u $dbUser -p$dbPass --default_character_set utf8 $dbName < $sql";
        exec($command, $out, $status);

        if (0 !== $status) {
            $this->app->error->addError('Ошибка загрузки db', $status);
        }
        return $status;
    }

    public function makeUsers(){
        $res = $this->app->dbTemp->query("SELECT * FROM wp_users");
        while ($user = $res->fetch(\PDO::FETCH_ASSOC)){
           $this->users[$user['ID']] = $user['user_login'];
        }
        return $this->users;
    }
    public function getUsers($file){
        $arResult = [];
        $res = $this->app->dbTemp->query("SELECT * FROM wp_users");
        while ($user = $res->fetch(\PDO::FETCH_ASSOC)){
            $user['file_name'] = $file;
            $arResult[] = $user;
        }
        return $arResult;
    }
    public function getOptions($file){
        $arResult = [];
        $arKey = [
            'siteurl','admin_email',
        ];
        $res = $this->app->dbTemp->query("SELECT * FROM wp_options");
        while ($row = $res->fetch(\PDO::FETCH_ASSOC)){
            if(in_array($row['option_name'], $arKey)){
                $row['file_name'] = $file;
                $arResult[] = $row;
            }

        }
        return $arResult;
    }

    public function getPosts($file){
        // из-за того что таблицы разные в рахных базах получаем конкртетную

        $result = $this->app->dbTemp->query("SHOW TABLES");
        while ($row = $result->fetch(\PDO::FETCH_NUM)){
           $tables[] = $row[0];
        }
        $postTable = 'wp_posts';
        foreach ($tables as $table) {
            if(strpos($table, "_posts") !== false){
                $postTable = $table;
            }
        }
        $arPosts = [];
        $res = $this->app->dbTemp->query("SELECT * FROM `$postTable`");
        while ($row = $res->fetch(\PDO::FETCH_ASSOC)){

            $row['file_name'] = $file;
            $row['post_type'] = 'post';
            $row['post_parent'] = '0';
            //$row['post_author'] = $this->users[$row['post_author']];
            $row['post_content'] = $this->formatText($row['post_content']);
            $arPosts[] = $row;
        }
        return $arPosts;
    }
    public function getCategory($file){
        $arResult = [];
        $res = $this->app->dbTemp->query("SELECT * FROM wp_terms");
        while ($row = $res->fetch(\PDO::FETCH_ASSOC)){
            $row['file_name'] = $file;
            $arResult[] = $row;
        }
        return $arResult;
    }

    public function formatText($text){
        // ссылки
        $text =  preg_replace ("!<a.*?href=\"?'?([^ \"'>]+)\"?'?.*?>(.*?)</a>!is", "\\2", $text);
        //картинки
        $text = preg_replace('/<img.*>/Uis', '', $text);
        return $text;
    }

    

}