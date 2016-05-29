<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 16/5/30
 * Time: 上午1:16
 */

require_once "../request_and_respond/DSDRequestResponder.php";
require_once "../bill_management/DSDBillManager.php";

class DSDRequestBillHandler {
    
    public static function modify() {
        DSDAuthorizationChecker::ensureIam(DSDAccountManager::USER);
        if ($_SERVER["REQUEST_METHOD"]=="POST") {
            Utils::ensureKeys($GLOBALS["data"], array("info"));
            $info = json_decode($GLOBALS["data"]["info"], true);
            if (!$info || count($info) == 0) {
                DSDRequestResponder::http_code(400, false);
                DSDRequestResponder::respond(false, "信息格式错误");
            }
            foreach ($info as $goods) {
                Utils::ensureKeys($goods, array("gid", "price", "sid", "number"));
            }
            $result = DSDBillManager::create($GLOBALS["data"]["info"]);
            DSDRequestResponder::respond(true, null, array("bill_id" => $result));
        } else if ($_SERVER["REQUEST_METHOD"]=="GET") {
            DSDRequestResponder::respond(true, null, DSDBillManager::get());
        }
    }
    
    public static function pay() {
        DSDAuthorizationChecker::ensureIam(DSDAccountManager::USER);
        Utils::ensureKeys($GLOBALS["data"], array("bill_id"));
        $price = DSDBillManager::pay($GLOBALS["data"]["bill_id"]);
        if ($price == 0) {
            DSDRequestResponder::respond(false, "订单错误");
        }else {
            DSDRequestResponder::respond(true, null, array("price" => $price));
        }
    }

    public static function comment() {
        DSDAuthorizationChecker::ensureIam(DSDAccountManager::USER);
        
    }
}