<?php

/**
 * Created by PhpStorm.
 * User: iodine
 * Date: 16/5/30
 * Time: 上午12:35
 */
require_once "../data_management/DSDFileStorageManager.php";
require_once "../data_management/DSDDatabaseConnector.php";
require_once "../request_and_respond/DSDRequestResponder.php";
require_once "../utils/Utils.php";

class DSDRequestFileHandler{
    public static function upload(){
        $public=$_GET["public"]=="true";
        if(!$public){
            DSDAuthorizationChecker::ensureIam(DSDAuthorizationChecker::LOGGEDIN);
        }
        $file=$_FILES["file"];
        $token=Utils::createRandom(64);
        DSDFileStorageManager::writeFileWithTokenFromPath($token, $file["tmp_name"]);
        $extpos=strrpos($file["name"], ".");
        if($extpos===false){
            $filename=$file["name"];
            $ext="";
        }else{
            $filename=substr($file["name"], 0, $extpos);
            $ext=substr($file["name"], $extpos+1);
        }
        DSDDatabaseConnector::insert("files", array(
            "fhash"=>$token,
            "owner_id"=>$public?0:DSDAuthorizationChecker::getCurrentUid(),
            "size"=>$file["size"],
            "ext"=>$ext,
            "file_name"=>$filename,
            "create_time"=>time(),
            "modify_time"=>time()
        ));
        DSDRequestResponder::respond(true, null, DSDDatabaseConnector::get_first_match("select * from files WHERE fid=:fid", array(":fid"=>DSDDatabaseConnector::getInsertId())));
    }
    public static function ensureRightsToToken($token){
        $owner=DSDDatabaseConnector::get_first_match("select owner_id from files WHERE fhash=:fhash", array(":fhash"=>$token), "owner_id");
        if($owner===null){
            DSDRequestResponder::http_code(404);
        }
        if($owner!=0&&$owner!=DSDAuthorizationChecker::getCurrentUid()){
            DSDRequestResponder::http_code(403);
        }
    }
    public static function ensureWriteRightsToToken($token){
        $owner=DSDDatabaseConnector::get_first_match("select owner_id from files WHERE fhash=:fhash", array(":fhash"=>$token), "owner_id");
        if($owner===null){
            DSDRequestResponder::http_code(404);
        }
        if($owner!=DSDAuthorizationChecker::getCurrentUid()||$owner==0){
            DSDRequestResponder::http_code(403);
        }
    }
    public static function modify($fhash){
        if($_SERVER["REQUEST_METHOD"]=="DELETE"){
            self::ensureWriteRightsToToken($fhash);
            DSDFileStorageManager::deleteFileWithToken($fhash);
            DSDDatabaseConnector::write("delete from files WHERE fhash=:fhash", array(":fhash"=>$fhash));
            DSDRequestResponder::respond(true);
        }elseif($_SERVER["REQUEST_METHOD"]=="GET"){
            self::ensureRightsToToken($fhash);
            $info=DSDDatabaseConnector::get_first_match("select file_name, ext from files WHERE fhash=:fhash", array(":fhash"=>$fhash));
            if(@$_GET["download"]=="1"){
                header("Content-type: application/force-download");
                Header("Content-Disposition: attachment;filename=".$info["file_name"].".".$info["ext"]);
            }
            echo DSDFileStorageManager::readFileWithToken($fhash);
        }elseif($_SERVER["REQUEST_METHOD"]=="PUT"){
            self::ensureWriteRightsToToken($fhash);
            $file=$_FILES["file"];
            DSDFileStorageManager::writeFileWithTokenFromPath($fhash, $file["tmp_name"]);
            $extpos=strrpos($file["name"], ".");
            if($extpos===false){
                $filename=$file["name"];
                $ext="";
            }else{
                $filename=substr($file["name"], 0, $extpos);
                $ext=substr($file["name"], $extpos+1);
            }
            DSDDatabaseConnector::update("files", array(
                "size"=>$file["size"],
                "ext"=>$ext,
                "file_name"=>$filename,
                "modify_time"=>time()
            ), "fhash=:fhash", array(":fhash"=>$fhash));
            DSDRequestResponder::respond(true);
        }
    }
}