<?php

$user_name = "sa";
$password = "Bismillah123";
$host_name = "172.16.20.200:3336";
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
    }
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
  ),
));

$response_login = curl_exec($curl_login);

curl_close($curl_login);
// echo $response_login, '<br>';
$login_obj = json_decode($response_login);


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
// echo $response_schedule;


$schedule_obj = json_decode($response_schedule);
if (isset($schedule_obj->error)) {
  echo $schedule_obj->error->message . '<br>';
  echo 'schedule error';
  exit();
}

$schedule_list = [];
foreach ($schedule_obj->result as $val) {
  if ($val->number != false) {
    $schedule_list[] = $val;
  }
}
echo count($schedule_list) . ' Schedule Found<br>';

foreach ($schedule_list as $schedule) {
  echo '====================== ' . $schedule->number . ' === ' . $schedule->id . ' ======================<br>';

  // $query_loads = 'select a.*, b.bp_name  FROM `V_BatchSetupTickets` a 
  //                 inner join Batching_plant b on a.BP_ID=b.id_bp 
  //                 where a.index_load > (select ifnull(max(ref),0) from SKLP_API_Gagal where task_code=\'' . $schedule->number . '\') 
  //                 or a.index_load > (select ifnull(max(ref),0) from SKLP_API_Log where task_code=\'' . $schedule->number . '\') 
  //                 and PO_Num=\'' . $schedule->number . '\' 
  //                 and Other_Code=\'' . $schedule->mutu[1] . '\' 
  //                 and Consistence=\'' . $schedule->slump[1] . '\' 
  //                 and Ticket_Status=\'O\'
  //                 ORDER BY `a`.`index_load` DESC';

  $query_loads = 'select a.*, b.bp_name  FROM `V_BatchSetupTickets` a 
                  inner join Batching_plant b on a.BP_ID=b.id_bp 
                  where a.index_load > (select ifnull(max(ref),(select ifnull(max(ref),0) from SKLP_API_Log where task_code=\'' . $schedule->number . '\')) from SKLP_API_Gagal where task_code=\'' . $schedule->number . '\') 
                  and (PO_Num=\'' . $schedule->number . '\' OR Job_Code=\'' . $schedule->number . '\')  
                  and Other_Code=\'' . $schedule->mutu[1] . '\' 
                  and Consistence=\'' . $schedule->slump[1] . '\' 
                  and Ticket_Status=\'O\'
                  ORDER BY `a`.`index_load` DESC';

  echo $query_loads . '<br>';
  $loads = mysqli_query($conmysql, $query_loads);

  echo mysqli_num_rows($loads) . ' loads<br>';

  while ($load = mysqli_fetch_array($loads)) {
    echo ' index load = ' . $load['index_load'] . '<br>';


    $plant_id = mysqli_query($conmysql, "select apb_plant_id from SKLP_Plant where bp_id=" . $load['BP_ID']);
    $plant_id = mysqli_fetch_array($plant_id);

    if (!empty($plant_id[0])) {
      echo 'plant id= ' . $plant_id[0] . '<br>';

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
                                        "args": [[["name","=", "' . $load['Truck_Code'] . '"]]],
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


      $truck_id = 43; //default truck id di master data api
      if (!empty($truck_obj->result)) {
        $truck_id = $truck_obj->result[0]->id;
      }
      echo 'Truck Id = ' . $truck_id . '<br>';



      // post sklp 
      // driver masih default belum dinamis
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
                "driver_id": 70,
                "apb_delivery_line": [[0, 0,
                    {
                        "volume": ' . $load['Load_Size'] . ',
                        "volume_comm": 0,
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
      // echo $response_post_sklp . '<br>';
      $post_sklp_obj = json_decode($response_post_sklp);
      if (isset($post_sklp_obj->result)){
        $submit='
            {"jsonrpc": "2.0",
              "params": {
                  "token": "af7eec1b5c53d61eb91392a6cf16d8241ec235391253d9242f68d5af9b12c351",
                  "model": "apb.delivery",
                  "method": "action_submit",
                  "args": ['. $post_sklp_obj->result .'], 
                  "context": {}
              }
            }' ;

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
        echo "<br>";
        echo "result post submit :" ;
        echo "<br>";
        echo $response_post_submit . '<br>';

      }

      if (isset($post_sklp_obj->error)) {
        echo $post_sklp_obj->error->data->message . '<br>';

        $query_post = "insert INTO `SKLP_API_Gagal`
                        (`task_code`,`task_description`, `ref`, `args`, `keterangan`, `status`) 
                        VALUES 
                        ('" . $schedule->number . "','" . $schedule->project[1] . "', " . $load['index_load'] . ", '" . $args . "', '" . $post_sklp_obj->error->data->message . "', 'gagal')";
        echo $query_post . '<br>';

        mysqli_query($conmysql, $query_post);
      } else if (isset($post_sklp_obj->result)) {
        echo 'post sukses <br><br>';

        $query_post = "insert INTO `SKLP_API_Log`
                        (`task_code`,`task_description`, `ref`, `args`, `keterangan`, `status`) 
                        VALUES 
                        ('" . $schedule->number . "','" . $schedule->project[1] . "', " . $load['index_load'] . ", '" . $args . "', 'sukses', 'sukses')";
        echo $query_post . '<br>';

        mysqli_query($conmysql, $query_post);
      }
      sleep(1);
    } else {
      echo 'BP ID ' . $load['BP_ID'] . ' tidak ada di SKLP_Plant';
      $query_post = "insert INTO `SKLP_API_Gagal`
                    (`task_code`,`task_description`, `ref`, `args`, `keterangan`, `status`) 
                    VALUES 
                    ('" . $schedule->number . "','" . $schedule->project[1] . "', " . $load['index_load'] . ", '', 'BP ID " . $load['BP_ID'] . " tidak ada di SKLP_Plant', 'gagal')";
      echo $query_post . '<br>';
      mysqli_query($conmysql, $query_post);
    }
  }
  echo '<br>';
  echo '<br>';
}
