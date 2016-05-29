<?php

/**
 * Created by PhpStorm.
 * User: iodine
 * Date: 16/5/30
 * Time: 上午12:30
 */
class DSDFileStorageManager{
    static $rootpath="/var/storage/";
    static function readFileWithToken($token){
        if(!Utils::strMatchReg($token, "[a-zA-Z0-9]+")){
            return false;
        }
        $realpath=DSDFileStorageManager::$rootpath . $token;
        return file_get_contents($realpath);
    }
    static function writeFileWithToken($token, $content){
        if(!Utils::strMatchReg($token, "[a-zA-Z0-9]+")){
            DSDRequestResponder::respond(false, "error when write file: 非法的token $token");
            return false;
        }
        $realpath=DSDFileStorageManager::$rootpath . $token;
        $res=file_put_contents($realpath, $content);
        if(!$res){
            DSDRequestResponder::respond(false, "error when write file: 权限不足");
            return false;
        }else{
            return true;
        }
    }
    static function writeFileWithTokenFromPath($token, $path){
        if(!Utils::strMatchReg($token, "[a-zA-Z0-9]+")){
            return false;
        }
        $realpath=DSDFileStorageManager::$rootpath . $token;
        return move_uploaded_file($path, $realpath);
    }
    static function deleteFileWithToken($token){
        if(!Utils::strMatchReg($token, "[a-zA-Z0-9]+")){
            return false;
        }
        $realpath=DSDFileStorageManager::$rootpath . $token;
        return @unlink($realpath);
    }
    static function copyFileWithToken($token, $newtoken){
        if(!Utils::strMatchReg($token, "[a-zA-Z0-9]+")){
            return false;
        }
        if(!Utils::strMatchReg($newtoken, "[a-zA-Z0-9]+")){
            return false;
        }
        return copy(DSDFileStorageManager::$rootpath. $token, DSDFileStorageManager::$rootpath. $newtoken);
    }
    static function fileSizeOfToken($token){
        if(!Utils::strMatchReg($token, "[a-zA-Z0-9]+")){
            return false;
        }
        $realpath=DSDFileStorageManager::$rootpath . $token;
        return filesize($realpath);
    }
    static function filePathOfToken($token){
        if(!Utils::strMatchReg($token, "[a-zA-Z0-9]+")){
            return false;
        }
        $realpath=DSDFileStorageManager::$rootpath . $token;
        return $realpath;
    }
}