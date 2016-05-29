<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 16/5/30
 * Time: 上午1:17
 */

require_once "../account_management/DSDAuthorizationChecker.php";
require_once "../data_management/DSDDatabaseConnector.php";
require_once "../shoppingcart_management/DSDShoppingcartManager.php";

class DSDBillManager {

    public static function create($info) {

        DSDDatabaseConnector::insert(
            "bill",
            array(
                "user_id" => DSDAuthorizationChecker::getCurrentUid(),
                "info" => $info,
                "timestamp" => time()
            )
        );
        $insert_id = DSDDatabaseConnector::getInsertId();

        foreach (json_decode($info, true) as $goods) {
            DSDShoppingcartManager::delete($goods["gid"], $goods["sid"]);
        }

        return $insert_id;
    }

    public static function get() {

        $result = DSDDatabaseConnector::read("select * from bill where user_id=:user_id", array("user_id" => DSDAuthorizationChecker::getCurrentUid()));
        for ($i = 0; $i < count($result); $i++) {
            $result[$i]["info"] = json_decode($result[$i]["info"], true);
        }
        return $result;
    }

    public static function pay($bill_id) {
        $price = 0;
        $result = DSDDatabaseConnector::get_first_match("select * from bill where bill_id=:bill_id", array("bill_id" => $bill_id));
        foreach (json_decode($result["info"], true) as $goods) {
            $price += $goods["price"];
        }
        return $price;
    }

    public static function comment($bill_id) {

    }

}