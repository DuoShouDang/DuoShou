<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 16/5/30
 * Time: 上午12:35
 */

require_once "../request_and_respond/DSDRequestResponder.php";
require_once "../data_management/DSDDatabaseConnector.php";

class DSDRequestCategoryHandler {

    public static function get() {
        DSDRequestResponder::respond(true, null,
            DSDDatabaseConnector::read("SELECT * FROM category")
        );
    }

}