<?php

use Autocall\Bitrix\BClass\BEway;
require_once __DIR__ . '/classes/BEway.php';

$arResult = BEway::getEway('crm.dealcategory.list');
$arUserResult = BEway::getEway('user.get');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/bootstrap.min.css" >
    <link rel="stylesheet" href="./css/bootstrap-grid.min.css" >
    <link rel="stylesheet" href="./css/bootstrap-reboot.min.css" >
    <link rel="stylesheet" href="./css/style.css">
    <title>Document</title>
</head>
<body>
    <input type="hidden" name="<?=$_REQUEST['DOMAIN']?>" id="<?=$_REQUEST['member_id']?>">
    <div class="app">
        <div class="mb-3">
            <div class="logo">
                <img src="./image/logo.png" alt="Pragma:Lirax">
            </div>
        </div>

        <div class="key-lirax">
            <div class="form-group row">
                <label for="key-lirax" class="col-sm-2 col-form-label col-form-label-sm">Ключ LiraX:</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control form-control-sm" id="key-lirax" placeholder="Key">
                </div>
            </div>
        </div>

        <div class="mb-3 active-funnels">
            <div class="accordion" id="accordionExample">
                <div class="card">
                    <div class="card-header funnels-header" id="headingTwo">
                        <h2 class="mb-0">
                            <div class="btn btn-link btn-block collapsed title-funnels" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Укажите активные воронки
                            </div>
                        </h2>
                    </div>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                        <div class="card-body">
                            <div class="table-section">
                                <table class="table table-striped">
                                    <tbody>
                                    <?foreach ($arResult["result"] as $key => $arItems):?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="js-ajax-request" id="<?=$arItems["ID"]?>">
                                        </td>
                                        <td>
                                            <label for="<?=$arItems["ID"]?>" id="<?=$arItems["ID"]?>"><?=$arItems["NAME"]?></label>
                                        </td>
                                    </tr>
                                    <?endforeach;?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="time-auto-call">
            <div class="form-group row">
                <label for="time-auto-call-title" class="col-sm-5 col-form-label col-form-label-sm">Время проверки дозвона до "Ответственного"</label>
                <div class="col-sm-2 d-flex">
                    <input type="text" class="form-control form-control-sm" id="time-auto-call-title" value="0">
                    <span class="pl-2">минут</span>
                </div>
            </div>
        </div>

        <div class="work-time">
            <div class="form-group row">
                <label for="work-time-title" class="col-sm-5 col-form-label col-form-label-sm">Робочее время:</label>
                <div class="col-sm-3 d-flex">
                    <span class="pr-2">c</span>
                    <input type="time" class="form-control form-control-sm" id="work-time-title" value="">
                    <span class="pl-2">до</span>
                    <input type="time" class="ml-2 form-control form-control-sm" id="work-end-time" value="">
                </div>
            </div>
        </div>

        <div class="quantity-request">
            <div class="form-group row">
                <label for="quantity-request-title" class="col-sm-5 col-form-label col-form-label-sm">Количество попыток дозвона до клиента:</label>
                <div class="col-sm-1">
                    <input type="text" class="form-control form-control-sm" id="quantity-request-title" value="">
                </div>
            </div>
        </div>

        <div class="work-quantity-request">
            <div class="container">
                <div class="row" id="clone-here">

                </div>
            </div>
        </div>

        <div class="work-funnels">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6" >
                        <input type="checkbox" data-settings="false" id="using-stores">
                        <label for="using-stores">Использование магазинов</label>
                        <div class="funnels-block">
                            <ul class="store-funnels-block-list">
                            <?foreach ($arResult["result"] as $key => $arItems):?>
                                <li class="store-funnels-block-item">
                                    <div class="form-group row">
                                        <label class="col-sm-6 col-form-label col-form-label-sm" for="stores-input-funnels-<?=$key?>" id="<?=$arItems["ID"]?>"><?=$arItems["NAME"]?></label>
                                        <div class="col-sm-3">
                                            <input class="form-control form-control-sm chek-number" type="number" id="stores-input-funnels-<?=$key?>">
                                        </div>
                                    </div>
                                </li>
                            <?endforeach;?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <input type="checkbox" data-settings="false" id="number-funnels">
                        <label for="number-funnels">Номера воронок</label>
                        <div class="funnels-block">
                            <ul class="number-funnels-block-list">
                                <?foreach ($arResult["result"] as $key => $arItems):?>
                                    <li class="number-funnels-block-item">
                                        <div class="form-group row">
                                            <label class="col-sm-6 col-form-label col-form-label-sm" for="number-input-funnels-<?=$key?>" id="<?=$arItems["ID"]?>"><?=$arItems["NAME"]?></label>
                                            <div class="col-sm-3">
                                                <input class="form-control form-control-sm chek-number" type="number" id="number-input-funnels-<?=$key?>">
                                            </div>
                                        </div>
                                    </li>
                                <?endforeach;?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="priority-user">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <input type="checkbox" data-settings="false" id="user-chek">
                        <label for="user-chek">Приоритет</label>
                    </div>
                        <div class="col-sm-12">
                            <div class="user-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Имя</th>
                                        <th>Фамилия</th>
                                        <th>Приоритет</th>
                                    </tr>
                                </thead>
                                <tbody class="js-table">
                                    <?foreach ($arUserResult["result"] as $key=>$arUItem):?>
                                        <? $key++;?>
                                        <tr class="user-list">
                                            <td><span class="number-list" id="<?=$arUItem["ID"]?>"><?=$key?></span></td>
                                            <td><span class="name-user"><?=$arUItem["NAME"]?></span></td>
                                            <td><span class="last-name-user"><?=$arUItem["LAST_NAME"]?></span></td>
                                            <td><input type="text" class="user-input"></td>
                                        </tr>
                                    <?endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="submit-settings">
            <button type="button" class="btn btn-success js-submit">Сохранить</button>
        </div>

        <div class="auto-call-modal">
            <div class="auto-call-modal-backdrop">
                <div class="auto-call-modal-content">

                </div>
            </div>
        </div>
    </div>

    <script src="./js/jqure.js"></script>
    <script src="./js/bootstrap.bundle.min.js"></script>
    <script src="./js/Settings.js"></script>
    <script src="./js/main.js"></script>
    <script src="./js/ajax.js"></script>
</body>
</html>