<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 16/5/29
 * Time: 下午11:17
 */

require_once "../utils/Utils.php";
require_once "../data_management/DSDDatabaseConnector.php";
require_once "../account_management/DSDAuthorizationChecker.php";

Class DSDShoppingcartManager {

    public static function update($gid, $sid) {
        DSDDatabaseConnector::update(
            "shopping_cart",
            Utils::filter($GLOBALS["data"], ["number!"]),
            "user_id=:uid AND good_id=:gid AND sort_identifier=:sid",
            array(
                ":uid"=>DSDAuthorizationChecker::getCurrentUid(),
                ":gid"=>$gid,
                ":sid"=>$sid
            )
        );
        return (DSDDatabaseConnector::getAffectedRows() > 0);
    }

    public static function add($gid, $sid) {
        return DSDDatabaseConnector::insert(
            "shopping_cart",
            array(
                "user_id"=>DSDAuthorizationChecker::getCurrentUid(),
                "good_id"=>$gid,
                "sort_identifier"=>$sid,
                "number"=>@$GLOBALS["data"]["number"]?$GLOBALS["data"]["number"]:1
            )
        );
    }

    public static function delete($gid, $sid) {
        return DSDDatabaseConnector::write("delete from shopping_cart WHERE user_id=:uid AND good_id=:gid AND sort_identifier=:sid", array(
            ":uid"=>DSDAuthorizationChecker::getCurrentUid(),
            ":gid"=>$gid,
            ":sid"=>$sid
        ));
    }
    
    public static function get() {
        return array_map(function($one){
            $info=DSDDatabaseConnector::get_first_match("select name, info from goods WHERE gid=:gid", array(":gid"=>$one["good_id"]));
            DSDGoodsManager::restore_info($info);
            $one["product_info"]=array_merge($info["info"][$one["sort_identifier"]], array("product_name"=>$one["name"]));
            $one["info"]=$info["info"];
            return $one;
        }, DSDDatabaseConnector::read("select good_id, sort_identifier, number from shopping_cart WHERE user_id=:uid",
            array(":uid"=>DSDAuthorizationChecker::getCurrentUid()))
        );
    }

}