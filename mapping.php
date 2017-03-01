<?php
include_once("core.php");

if (isset($_SERVER['HTTP_ORIGIN'])) {
    // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
    // you want to allow, and if so:
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        // may also be using PUT, PATCH, HEAD etc
        header("Access-Control-Allow-Methods: GET, POST,PUT,DELETE, OPTIONS,PATCH");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}


if (false && !isLogin()) {
    header("HTTP/1.1 401 Unauthorized");
    header('location: ../login.php?rederict=new/');
    echo "ERROR";
    exit;
}
header('Content-Type: application/json');

$routeAaction = substr($_SERVER['REQUEST_URI'], 0, strlen($_SERVER['REQUEST_URI']) - strlen($_SERVER['QUERY_STRING']) - 1);
$routeAaction = explode("?", $_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
$routeAaction = explode("/", $routeAaction[0], FILTER_SANITIZE_URL);
$routeAaction = $routeAaction[count($routeAaction) - 1];
if (ctype_digit($routeAaction)) {
    $itemId = intval($routeAaction, 10);
    if ($itemId <= 0) {
        $itemId = null;
    }
    $routeAaction = null;
} else {
    $itemId = null;
}

if($routeAaction != null && strrpos($routeAaction,".php")>-1){
    $routeAaction=null;
}
/* @var $DBcontext DataBase */

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $routeAaction == null) {


    $limit =(isset($_GET['limit']) && ctype_digit($_GET['limit']) && intval($_GET['limit'], 10)>0) ? intval($_GET['limit'], 10) : 10;
    $offset =(isset($_GET['offset']) && ctype_digit($_GET['offset']) && intval($_GET['offset'], 10)>0) ? intval($_GET['offset'], 10) : 0;

    $query = "SELECT  * from * limit :seek, :take ";
    $request = $DBcontext->ExercuteQuery($query, array('seek' => $offset, 'take' => $limit));
    echo json_encode($request);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $routeAaction == 'count') {


    $query = "SELECT  count(*) as totalRecord from map_channel";
    $request = $DBcontext->ExercuteQuery($query);
    echo json_encode($request[0]);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // The request is using the POST method
}
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // print_r($_SERVER);
///    print_r($_REQUEST);
    //   print_r($_GET);
    //  print_r($_PUT);
    // print_r($_POST);
    $put_data = file_get_contents("php://input");

    $request = json_decode($put_data);

    $request = array(
        'modifiedDate' => date("Y-m-d H:i:s")
    );

    echo json_encode($request);
    exit;
    // The request is using the PUT method
}
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // The request is using the DELETE method
}
if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {

    if ($itemId == null) {
        header($_SERVER['SERVER_PROTOCOL'] . ' ' . '404 Not Found', true, 404);
        exit;
    }
    $put_data = file_get_contents("php://input");

    $request = json_decode($put_data);



    $sqlParam = array(
        'Modified_Date' => gmdate("Y-m-d H:i:s"),
        'id' => $itemId,
        'Modified_By' => $user['id']

    );

    $responce = array(
        'modifiedDate' => gmdate("Y-m-d H:i:s"),
        'modifiedBy' => $user['name']
    );
    $paramForLog = array();
    foreach ($request as $key => $val) {
        switch ($key) {
            case "medium":
                $sqlParam['medium'] = $val;

                break;
            case "source":
                $sqlParam['source'] = $val;
                break;
            case "channel": {
                switch ($val) {
                    case "Email":
                    case "Direct Mail":
                        $sqlParam['direction'] = "Outbound";
                        $sqlParam['channel'] = $val;
                        break;
                    case "Social":
                    case "Programmatic Advertising":
                    case "QR":
                    case "CPC":
                    case "Public Relations":
                        $sqlParam['direction'] = "Inbound";
                        $sqlParam['channel'] = $val;
                        break;
                    case "Direct":
                    case "Referral":
                    case "Search":
                        $sqlParam['direction'] = "Organic";
                        $sqlParam['channel'] = $val;
                        break;
                }
                $sqlParam['channel'] = $val;
                $responce['direction'] = $sqlParam['direction'];
                break;
            }
        }
    }


    if (count($sqlParam) <= 3) {
        header($_SERVER['SERVER_PROTOCOL'] . ' ' . '500 No Content', true, 500);
    }
    $preperadSqlParam = array();
    foreach ($sqlParam as $key => $val) {
        if ($key == "id")
            continue;
        $preperadSqlParam[] = $key . " = :" . $key;
    }

    $itemOldValue = $DBcontext->ExercuteQuery("select *
            from * 
            where id=:id", array("id" => $itemId))[0];


    $sqlQuery = 'UPDATE map_channel SET ' . join(" , ", $preperadSqlParam) . ' WHERE id=:id';

    $DBcontext->ExercuteQuery($sqlQuery, $sqlParam);

    foreach($sqlParam as $key => $val) {
        if ($key == "Modified_Date"
            || $key == "id"
            || $key == "Modified_By"
        )
            continue;


        $paramForLog = array(
            'from_value' => $itemOldValue[$key],
            'to_value' => $val,
            'field_name' => $key,
            'user_id' => $user['id'],
            'row_id' => $itemId,
            'action' => 'edit'

        );
        $query = "INSERT INTO * (user_id, row_id, action, field_name, from_value, to_value) 
          VALUES (:user_id, :row_id, :action, :field_name, :from_value, :to_value)";
        $DBcontext->ExercuteQuery($query, $paramForLog);
    }
    echo json_encode($responce);
    exit;
}

header($_SERVER['SERVER_PROTOCOL'] . ' ' . '404 Forbidden', true, 404);
exit;
