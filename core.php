<?php

include('../cfg/app.php');
define('_DEBUG_', true);
define('_USE_AUTH_', false);

spl_autoload_register(function ($class_name) {
    include $class_name . '.class.php';
});
/*INITIALIZATION*/

$sqlFields = Array();

$DBcontext = new DataBase($mysqli);





/*

        global $DBcontext, $sqlFields;

        $queryToDB = $DBcontext->change_log();
        $getParams = $request->getQueryParams();


        if (isset($getParams["count"]))
            $queryToDB->Take($getParams["count"]);
        if (isset($getParams["position"]))
            $queryToDB->Skip($getParams["position"]);
        return $response->withJson($queryToDB->OrderBy('action_date')->ToArray());

    });
*/


function isLogin()
{

    return login_check();

}
