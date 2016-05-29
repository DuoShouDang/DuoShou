<?php

/**
 * Created by PhpStorm.
 * User: iodine
 * Date: 16/5/25
 * Time: 下午8:00
 */
require_once "../request_and_respond/DSDRequestResponder.php";
require_once "../goods_management/DSDGoodsManager.php";
require_once "../shoppingcart_management/DSDShoppingcartManager.php";

class DSDRequestShoppingcartHandler{

    public static function modify($gid, $sid) {
        DSDAuthorizationChecker::ensureIam(DSDAccountManager::USER);
        $goods = DSDGoodsManager::view_certain_goods($gid);
        if (!$goods) {
            DSDRequestResponder::http_code(404, false);
            DSDRequestResponder::respond(false, "商品不存在");
        }
        if ($goods["info"][$sid] == null) {
            DSDRequestResponder::http_code(404, false);
            DSDRequestResponder::respond(false, "类别不存在");
        }

        if ($_SERVER["REQUEST_METHOD"]=="PUT") {
            if (DSDShoppingcartManager::update($gid, $sid)) {
                DSDRequestResponder::respond(true);
            } else {
                DSDRequestResponder::respond(false, "该商品未加入购物车或信息没有改动");
            }
        } else if($_SERVER["REQUEST_METHOD"]=="POST") {
            Utils::ensureKeys($GLOBALS["data"], array("number"));
            if (!DSDShoppingcartManager::add($gid, $sid)) {
                DSDRequestResponder::respond(false, "该商品已加入购物车");
            } else {
                DSDRequestResponder::respond(true);
            }
        } else if($_SERVER["REQUEST_METHOD"]=="DELETE") {
            DSDRequestResponder::respond(DSDShoppingcartManager::delete($gid, $sid));
        }
    }

    public static function get() {
        DSDAuthorizationChecker::ensureIam(DSDAccountManager::USER);
        DSDRequestResponder::respond(true, null, DSDShoppingcartManager::get());
    }
}