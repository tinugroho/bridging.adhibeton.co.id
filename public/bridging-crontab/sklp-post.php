<?php

$user_name = "sa";
$password = "Bismillah123";
// $host_name = "172.16.20.200:3336";
$host_name = "172.16.200.104:3336";
$database = "db_bridging";
$database_ab = "db_autobatch";

$conmysql = mysqli_connect($host_name, $user_name, $password, $database);
if (mysqli_connect_errno()) {
  printf("Connect failed: %s\n", mysqli_connect_error());
  exit();
} else {
  //echo 'db konek<br>';
}

$con_ab = mysqli_connect($host_name, $user_name, $password, $database_ab);
if (mysqli_connect_errno()) {
  printf("Connect failed: %s\n", mysqli_connect_error());
  exit();
} else {
  //echo 'db konek<br>';
}


// =============== Login =========================================
$curl_login = curl_init();
curl_setopt_array($curl_login, array(
  CURLOPT_URL => 'https://apb.garudea.com/json-call/user_authenticate',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_COOKIESESSION => true,
  // CURLOPT_COOKIEJAR => __DIR__ . '/sklp_session.txt',
  // CURLOPT_COOKIEFILE => __DIR__ . '/sklp_session.txt',
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => '{
    "jsonrpc": "2.0",
    "params": {
        "login": "adm_bp@apb.com",
        "password": "APB2021",
        "db": "apbdev"
    }}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
  ),
));

$response_login = curl_exec($curl_login);

curl_close($curl_login);
// echo $response_login, '<br>';
$login_obj = json_decode($response_login);
$result['status'] = !empty($login_obj->result) ? 'SKLP Response' : 'SKLP Not Response';

// ==============================================================================

// Edit day of schedule==========================================================
$waktu = date("Y-m-d");

$curl_schedule = curl_init();

curl_setopt_array($curl_schedule, array(
  CURLOPT_URL => 'https://apb.garudea.com/json-call-schedule',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_COOKIESESSION => true,
  CURLOPT_COOKIEFILE =>  __DIR__ . '/sklp_session.txt',
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => '{"jsonrpc": "2.0",
    "params": {
        "token": "' . $login_obj->result->global_token . '",
        "model": "project.schedule.line",
        "method": "search_read",
        "args": [[["scheduled_date", ">=", "' . date('Y-m-d', strtotime('-3 day', strtotime($waktu))) . '"]]], 
        "context": {}
    }
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
  ),
));

$response_schedule = curl_exec($curl_schedule);

curl_close($curl_schedule);
// echo $response_schedule;


$schedule_obj = json_decode($response_schedule);
if (isset($schedule_obj->error)) {
  $result['schedule'] = $schedule_obj->error->message;
  exit();
}

$result['schedule'] = $schedule_obj->result;
$result['posted'] = [];

foreach ($schedule_obj as $schedule) {
  if ($schedule->number != false) {

    // $query_loads = 'select a.*, b.bp_name  FROM `V_BatchSetupTickets` a 
    //                 inner join Batching_plant b on a.BP_ID=b.id_bp 
    //                 where a.index_load > (select ifnull(max(ref),0) from SKLP_API_Gagal where task_code=\'' . $schedule->number . '\') 
    //                 or a.index_load > (select ifnull(max(ref),0) from SKLP_API_Log where task_code=\'' . $schedule->number . '\') 
    //                 and PO_Num=\'' . $schedule->number . '\' 
    //                 and Other_Code=\'' . $schedule->mutu[1] . '\' 
    //                 and Consistence=\'' . $schedule->slump[1] . '\' 
    //                 and Ticket_Status=\'O\'
    //                 ORDER BY `a`.`index_load` DESC';

    //Cek indexload terbesar dari api gagal Luqman 24/05/2023
    // $query_cek = "SELECT ref FROM SKLP_API_Gagal
    //                   where task_code = '" . $schedule->number . "'
    //                   union 
    //               SELECT ref from SKLP_API_Log
    //                   where task_code = '" . $schedule->number . "'
    //                   ORDER by ref DESC";
    // $max_ref = mysqli_query($conmysql, $query_cek);

    // var_dump($max_ref['ref']);

    // echo $max_ref;

    // echo $schedule->number;





    // $query_loads = 'select a.*, b.bp_name  FROM `V_BatchSetupTickets` a 
    //                 inner join Batching_plant b on a.BP_ID=b.id_bp 
    //                 where a.index_load > (select ifnull(max(c.ref),(select ifnull(max(d.ref),0) from SKLP_API_Log d where d.task_code=\'' . $schedule->number . '\')) from SKLP_API_Gagal c where c.task_code=\'' . $schedule->number . '\') 
    //                 and (PO_Num=\'' . preg_replace('/\s+/', '', $schedule->number)  . '\' OR Job_Code=\'' . preg_replace('/\s+/', '', $schedule->number) . '\')  
    //                 and a.Other_Code=\'' . preg_replace('/\s+/', '', strtoupper($schedule->mutu[1]))  . '\' 
    //                 and a.Consistence=\'' . preg_replace('/\s+/', '', $schedule->slump[1])  . '\' 
    //                 and a.Ticket_Status=\'O\'
    //                 ORDER BY `a`.`index_load` DESC';
    $query_loads = 'select a.*, b.bp_name  FROM `V_BatchSetupTickets` a 
          inner join Batching_plant b on a.BP_ID=b.id_bp 
          where a.index_load > (select ifnull(max(c.ref),(select ifnull(max(d.ref),0) from SKLP_API_Log d where d.task_code=\'' . $schedule->number . '\')) from SKLP_API_Gagal c where c.task_code=\'' . $schedule->number . '\') 
          and (PO_Num=\'' . preg_replace('/\s+/', '', $schedule->number)  . '\' OR Job_Code=\'' . preg_replace('/\s+/', '', $schedule->number) . '\')  
          and upper(a.Other_Code)=\'' . preg_replace('/\s+/', '', strtoupper($schedule->mutu[1]))  . '\' 
          and a.Ticket_Status=\'O\'
          ORDER BY `a`.`index_load` DESC';
    $loads = mysqli_query($conmysql, $query_loads);

    while ($load = mysqli_fetch_array($loads)) {
      $plant_id = mysqli_query($conmysql, "select apb_plant_id from SKLP_Plant where bp_id=" . $load['BP_ID']);
      $plant_id = mysqli_fetch_array($plant_id);

      if (!empty($plant_id[0])) {
        // get id truck
        $curl_truck = curl_init();
        curl_setopt_array($curl_truck, array(
          CURLOPT_URL => 'https://apb.garudea.com/json-call',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_COOKIESESSION => true,
          CURLOPT_COOKIEFILE =>  __DIR__ . '/sklp_session.txt',
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => ' {"jsonrpc": "2.0",
                    "params": {
                        "token": "' . $login_obj->result->global_token . '",
                        "model": "apb.truck",
                        "method": "search_read",
                        "args": [[["name","=", "' . preg_replace('/\s+/', '', strtoupper($load['Truck_Code'])) . '"]],["id","display_name"]],
                        "context": {}
                    }
                }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
          ),
        ));

        $response_truck = curl_exec($curl_truck);
        curl_close($curl_truck);

        $truck_obj = json_decode($response_truck);

        if (!empty($truck_obj->result)) {
          $truck_id = $truck_obj->result[0]->id;
        } else {
          $truck_id = 31; // TM036default truck id di master data api T
        }

        // post sklp 
        // driver masih default belum dinamis
        $driver = 90; //driver 6
        $sender = 70; //Admin Batching Plant
        $args = ' 
          {"jsonrpc": "2.0",
          "params": {
          "token": "' . $login_obj->result->global_token . '",
          "model": "apb.delivery",
          "method": "create",
          "args": [{
          "schedule_id": ' . $schedule->id . ',
          "name": "' . $load['bp_name'] . '-' . $load['Ticket_Code'] . '",
          "apb_plant_id": ' . $plant_id[0] . ',
          "date": "' . $load['RecordDate'] . '",
          "apb_truck_id": ' . $truck_id . ',
          "driver_id": ' . $driver . ', 
          "sender_id": ' . $sender . ',
          "apb_delivery_line": [[0, 0,
              {
                  "volume": ' . $load['Load_Size'] . ',
                  "volume_comm": ' . $load['Delivered_Qty'] . ',
                  "description": "' . $load['Address_Line3'] . '"
              }
          ]]
          }],
          "context": {}
          }
          }';

        $curl_post_sklp = curl_init();
        curl_setopt_array($curl_post_sklp, array(
          CURLOPT_URL => 'https://apb.garudea.com/json-call',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_COOKIESESSION => true,
          CURLOPT_COOKIEFILE =>  __DIR__ . '/sklp_session.txt',
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $args,
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
          ),
        ));

        $response_post_sklp = curl_exec($curl_post_sklp);

        curl_close($curl_post_sklp);
        $post_sklp_obj = json_decode($response_post_sklp);
        if (isset($post_sklp_obj->result)) {
          $submit = '
            {"jsonrpc": "2.0",
            "params": {
            "token": "' . $login_obj->result->global_token . '",
            "model": "apb.delivery",
              "method": "action_submit",
              "args": [' . $post_sklp_obj->result . '], 
              "context": {}
            }
            }';
          $curl_post_submit = curl_init();
          curl_setopt_array($curl_post_submit, array(
            CURLOPT_URL => 'https://apb.garudea.com/json-call',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_COOKIESESSION => true,
            CURLOPT_COOKIEFILE =>  __DIR__ . '/sklp_session.txt',
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $submit,
            CURLOPT_HTTPHEADER => array(
              'Content-Type: application/json',
            ),
          ));

          $response_post_submit = curl_exec($curl_post_submit);
        }

        if (isset($post_sklp_obj->error)) {

          $query_post = "insert INTO `SKLP_API_Gagal`
            (`task_code`,`task_description`, `ref`, `args`, `keterangan`, `status`) 
            VALUES 
            ('" . $schedule->number . "','" . $schedule->project[1] . "', " . $load['index_load'] . ", '" . $args . "', '" . $post_sklp_obj->error->data->message . "', 'gagal')";
          mysqli_query($conmysql, $query_post);
        } else if (isset($post_sklp_obj->result)) {
          $query_post = "insert INTO `SKLP_API_Log`
            (`task_code`,`task_description`, `ref`, `args`, `keterangan`, `status`) 
            VALUES 
            ('" . $schedule->number . "','" . $schedule->project[1] . "', " . $load['index_load'] . ", '" . $args . "', 'sukses', 'sukses')";
          mysqli_query($conmysql, $query_post);
        }
      } else {
        $query_post = "insert INTO `SKLP_API_Gagal`
          (`task_code`,`task_description`, `ref`, `args`, `keterangan`, `status`) 
          VALUES 
          ('" . $schedule->number . "','" . $schedule->project[1] . "', " . $load['index_load'] . ", '', 'BP ID " . $load['BP_ID'] . " tidak ada di SKLP_Plant', 'gagal')";
        mysqli_query($conmysql, $query_post);
      }

      $posted = [
        'sklp_bp_id'     => $plant_id[0],
        'sklp_driver_id' => $driver,
        'sklp_sender_id' => $sender,
        'bp_name'        => $load['bp_name'],
        'tiket_no'       => $load['Ticket_Code'],
        'taskcode'       => $schedule->number,
        'load_size'      => $load['Load_Size'],
        'vol_com'        => !empty($load['Delivered_Qty']) ? $load['Delivered_Qty'] : 0,
        'description'    => !empty($load['Address_Line3']) ? $load['Address_Line3'] : '',
      ];
      array_push($result['posted'], $posted);
    }
  }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($result, JSON_PRETTY_PRINT);
