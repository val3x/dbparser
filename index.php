<?php
header('Content-Type: text/html; charset=utf-8');
//лимиты
include 'app/Bootstrap.php';
$app = new \App\Bootstrap();
if($_REQUEST['ajax'] == "Y"){
    include 'app/ajax.php';
    die;
}
$arErrors = $app->getErrors();
$arFileSql = $app->getDBFiles();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DBParser</title>
    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="assets/css/theme.css" rel="stylesheet">
    
</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="/">Стартова</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>
<div class="container theme-showcase" role="main">
    <div class="page-header">
        <h1>DBparser</h1>

        <div class="alert alert-danger" role="alert" style="<?if(empty($arErrors)):?>display: none<?endif;?>">
            <strong>В системе найдены ошибки!</strong>
            <ul>
                <? foreach ($arErrors as $error) :?>
                    <li><?=$error?></li>
                <?endforeach;?>
            </ul>
        </div>
        <div id="dbList">
            <div class="row">
                <div class="col-sm-7">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Выберете Базу Данных</h3>
                        </div>
                        <div class="panel-body">
                           <select class="form-control" name="dbFiles" multiple="multiple" id="dbFiles" style="height: 300px;">
                               <? foreach ($arFileSql as $file):?>
                                   <option value="<?=$file?>"><?=$file?></option>
                               <?endforeach?>
                           </select>
                        </div>
                        <form enctype="multipart/form-data" method="post" style="overflow: hidden">
                            <p style="float: left;padding-left: 15px;"><input type="file" name="f[]" multiple accept="application/sql"></p>
                            <p style="float: right;padding-right: 15px;"><input type="submit" value="Отправить" class="btn btn-info"></p>
                        </form>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">Выберете данные для объединения</h3>
                        </div>
                        <div class="panel-body" id="dbMerge">
                            Выбирете минимум 2 базы
                        </div>
                    </div>
                </div>
                <div style="clear: both"></div>
                <p id="gobot">
                    <button type="button" id="gogo" class="btn btn-lg btn-success">Конвертировать</button>
                </p>
            </div>
        </div>
        <div id="dbResult">

        </div>

    </div>

</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/init.js"></script>
</body>
</html>
