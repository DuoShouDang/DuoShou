<?php

/**
 * Created by PhpStorm.
 * User: iodine
 * Date: 16/5/21
 * Time: 上午12:44
 */
require_once "../request_and_respond/DSDRequestResponder.php";
require_once "../account_management/DSDAccountManager.php";
require_once "../account_management/DSDAuthorizationChecker.php";

class DSDRequestAccountHandler{
    public static function register(){
        self::registerAccount(DSDAccountManager::USER);
    }

    public static function registerAccount($type){
        if (!Utils::is_email($GLOBALS["data"]["email"])) {
            DSDRequestResponder::respond(false, "Email格式错误");
        }
        if (strtolower($GLOBALS["data"]["password"]) == "d41d8cd98f00b204e9800998ecf8427e") {
            DSDRequestResponder::respond(false, "密码不能为空");
        }
        if (strlen($GLOBALS["data"]["username"]) > 30) {
            DSDRequestResponder::respond(false, "用户名过长");
        }
        Utils::ensureKeys($GLOBALS["data"], ["username", "email", "password"]);
        if(!$uid=DSDAccountManager::addAccount($GLOBALS["data"]["username"], $GLOBALS["data"]["email"],$type, $GLOBALS["data"]["password"])){
            DSDRequestResponder::respond(false, "Email已经被注册过了");
        }
        DSDRequestResponder::respond(true, null, DSDAccountManager::issueAccessTokenWithID($uid));
    }

    public static function login(){
        self::loginWithType(DSDAccountManager::USER);
    }
    public static function logout(){
        $res = DSDAccountManager::invalidateAccessToken(DSDAuthorizationChecker::getCurrentToken());
        DSDRequestResponder::respond($res);
    }
    public static function loginWithType($type){
        $res=DSDAccountManager::checkAccount($GLOBALS["data"]["email"], $GLOBALS["data"]["password"], $type);
        if($res["success"]){
            DSDRequestResponder::respond(true, null, array(
                "token"=>DSDAccountManager::issueAccessTokenWithID(DSDAccountManager::uidForEmail($GLOBALS["data"]["email"])),
                "type"=>$type
            ));
        }else{
            DSDRequestResponder::respond(false, $res["msg"]);
        }
    }
}