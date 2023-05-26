<?php

$user_name = "sa";
$password = "Bismillah123";
// $host_name = "db.bridging.adhibeton.co.id:3336";
$host_name = "172.16.200.104:3336";
$database_ab = "db_autobatch";

$conmysql = mysqli_connect($host_name, $user_name, $password, $database_ab);
if (mysqli_connect_errno()) {
  printf("Connect failed: %s\n", mysqli_connect_error());
  exit();
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
    }
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
  ),
));

$response_login = curl_exec($curl_login);

curl_close($curl_login);
$login_obj = json_decode($response_login);
$result['login'] = $login_obj;


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
        "args": [[["scheduled_date", ">=", "' . date('Y-m-d', strtotime('-7 day', strtotime($waktu))) . '"]]], 
        "context": {}
    }
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
  ),
));

$response_schedule = curl_exec($curl_schedule);
curl_close($curl_schedule);
$schedule_obj = json_decode($response_schedule);
$result['schedule'] = $schedule_obj;

if (isset($schedule_obj->error)) {
  $result['schedule'] = $schedule_obj->error->message;
  exit();
}

$schedule_list = [];
foreach ($schedule_obj->result as $val) {
  if ($val->number != false) {
    $schedule_list[] = $val;
  }
}
$result['schedule_list'] = $schedule_list;

foreach ($schedule_list as $key => $schedule) {

  $query_loads = 'SELECT BP_Code AS bp_name
                ,Ticket_Id as Ticket_Code
                ,Qty_Jobmix as Load_Size
                ,Truck as Truck_Code
                ,Driver as Driver_Name
                ,Createdate as RecordDate
                ,jh.Other_Code as Other_Code
                from TICKET t
                left join JOBMIX_HEADER jh on jh.Jobmix_Id = t.Jobmix_Id 
                where PO_Number = \'' . preg_replace('/\s+/', '', $schedule->number)  . '\' 
                and jh.Other_Code = \'' . preg_replace('/\s+/', '', strtoupper($schedule->mutu[1])) . '\' 
                ORDER BY t.index_load DESC';

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
                                        "args": [[["name","=", "' . preg_replace('/\s+/', '', $load['Truck_Code'])  . '"]]],
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
                          "volume_comm": ' . !empty($load['Delivered_Qty']) ? $load['Delivered_Qty'] : 0 . ',
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
                  "token": "af7eec1b5c53d61eb91392a6cf16d8241ec235391253d9242f68d5af9b12c351",
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

        curl_close($curl_post_submit);
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
      sleep(1);
    } else {
      $query_post = "insert INTO `SKLP_API_Gagal`
                    (`task_code`,`task_description`, `ref`, `args`, `keterangan`, `status`) 
                    VALUES 
                    ('" . $schedule->number . "','" . $schedule->project[1] . "', " . $load['index_load'] . ", '', 'BP ID " . $load['BP_ID'] . " tidak ada di SKLP_Plant', 'gagal')";
      mysqli_query($conmysql, $query_post);
    }
  }
  $result['action'][$key] = [
    'load_query' => $query_loads,
    'load_result' => $loads,
    'args_post' => $args,
    'response_delivery' => $post_sklp_obj,
    'response_submit' => $response_post_submit,
  ];
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
