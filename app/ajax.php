<?php

$action = $_REQUEST["action"];

switch ($action) {
    case 'importTestDB':

        $arResult['status'] = 'ok';
        $arMergeFiles = [];
        $arXmlFiles = [];
        // очищаем таблицы
        $arTables = [
            'category', 'options', 'posts', 'users'
        ];
        foreach ($arTables as $table) {
            $app->db->query("TRUNCATE TABLE  $table");
        }

        $arFiles = explode("|", $_REQUEST['file']);
        if (!empty($_REQUEST['merge']))
            $arMergeFiles = explode("|", $_REQUEST['merge']);
        //грузим во временную базу
        foreach ($arFiles as $file) {
            if (!empty($file)) {
                $import = new App\Repository\Import($app);
                $import->run($file);
                unset($import);
                //дадим мускулу немного отдохнуть перед след импортом
                sleep(5);
            }

        }
        //создаем xml
        foreach ($arFiles as $file) {
            if (!empty($file) && !in_array($file, $arMergeFiles)) {
                $xml = new App\Repository\Xml($app);
                $xmlFile = $xml->run($file);
                $arXmlFiles[$file]['filexml'] = $xmlFile;
                $arXmlFiles[$file]['file'] = $file;
                unset($xml);
                sleep(1);
            }
        }
        if (!empty($arMergeFiles)) {
            $xml = new App\Repository\Xml($app);
            $xmlFile = $xml->run(FALSE, $arMergeFiles);
            $arXmlFiles['merge']['filexml'] = $xmlFile;
            $arXmlFiles['merge']['file'] = 'merge';
            unset($xml);
        }

        $arResult['files'] = $arXmlFiles;
        echo json_encode($arResult);
        break;
    case '':

        break;
}