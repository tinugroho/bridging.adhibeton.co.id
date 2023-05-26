<?php
// $serverName = "103.253.113.16, 1433"; //serverName\instanceName, portNumber (default is 1433)
// $connectionInfo = array( "Database"=>"BP2_EBATCH_SADANG", "UID"=>"sa", "PWD"=>"Bismillah123");
// $conn = sqlsrv_connect( $serverName, $connectionInfo);

// if( $conn ) {
     // // echo "Connection established.<br />";
        // // if(($result = sqlsrv_query($conn,"select top 10 * from ITEM_PERIOD_USAGE ")) !== false){
        // // while( $row = sqlsrv_fetch_array( $result )) {
              // // //echo $obj->CreateDate.'<br />';
                          // // print("<pre>".print_r($row,true)."</pre>");
        // // }
    // // }
// }else{
     // echo "Connection could not be established.<br />";
     // die( print_r( sqlsrv_errors(), true));
// }


$user_name = "sa";
$password = "Bismillah123";
$host_name = "172.16.200.104:3336";
$database = "db_bridging";
$database_ab = "db_autobatch";
$database_eu = "db_eurotech";

$conmysql = mysqli_connect($host_name, $user_name, $password, $database);
if (mysqli_connect_errno()){
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
}else{
        //echo 'db konek<br>';
}

$con_ab = mysqli_connect($host_name, $user_name, $password, $database_ab);
if (mysqli_connect_errno()){
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
}else{
        //echo 'db konek<br>';
}


$con_eu = mysqli_connect($host_name, $user_name, $password, $database_eu);
if (mysqli_connect_errno()){
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
}else{
        //echo 'db konek<br>';
}
?>
