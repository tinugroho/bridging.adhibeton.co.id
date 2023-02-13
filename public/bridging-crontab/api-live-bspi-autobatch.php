<style>
	.red {
		color: red;
	}

	.blue {
		color: blue;
	}
</style>
<?php
require_once "koneksi.php";


$url = "http://192.168.100.17/apberp/erp/eaccounting/tools/api/sfservice.cfc?method=getDataBSPI";
//  Initiate curl
$ch = curl_init();
// Will return the response, if false it print the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Set the url
curl_setopt($ch, CURLOPT_URL, $url);
// Execute
$result = curl_exec($ch);
// Closing
curl_close($ch);

// Will dump a beauty json :3
$result = json_decode($result, true);
// var_dump($result);

//kosongkan tabel
if (!empty($result['databsp'])) {

	/* set default timezone */
	date_default_timezone_set("Asia/Jakarta");
	$timestamp = date('Y-m-d H:m:s');


	///aray konversi dari api
	$urlKonversi = "http://192.168.100.17/apberp/erp/eaccounting/tools/api/sfservice.cfc?method=getDataDensity";
	//  Initiate curl
	$chKonversi = curl_init();
	// Will return the response, if false it print the response
	curl_setopt($chKonversi, CURLOPT_RETURNTRANSFER, true);
	// Set the url
	curl_setopt($chKonversi, CURLOPT_URL, $urlKonversi);
	// Execute
	$resultKonversi = curl_exec($chKonversi);
	// Closing
	curl_close($chKonversi);

	// Will dump a beauty json :3
	$resultKonversi = json_decode($resultKonversi, true);
	$arrKonversi = $resultKonversi['dataitem'];
	// var_dump($arrKonversi);


	//isi ulang tabel
	foreach ($result['databsp'] as $jo) {
		if ($jo['Last_Update'] == '') {
			$jo['Last_Update'] = '2019-01-01';
		}

		//ARRAY URUTAN ITEMS
		$arrItems = explode('|', $jo['Material_Code']);

		//ARRAY SATUAN ITEMS
		$arrSatuanItems = explode('|', $jo['Material_Unit']);

		//CARI ID LAST POST JO NUMBER
		$query = "	select index_load from API_Logs_Detail where Method = 'POST' and JO_Number = '" . $jo['JO_Number'] . "' and `type` = 'live-bspi' 
					union
					(select index_load from API_Post_Gagal where JO_Number = '" . $jo['JO_Number'] . "' and `type` = 'live-bspi')
					order by index_load desc limit 1";
		// echo $query . "<br>";
		$last_post = mysqli_query($con_ab, $query);

		$last_post_id = "";
		while ($row = mysqli_fetch_array($last_post)) {
			$last_post_id = " and index_load > " . $row['index_load'] . " ";
		}

		// $apiupdate = " and a.RecordDate > '2021-06-08 13:06'";
		$apiupdate = "";

		//SELECT LOAD JO NUMBER
		$query = "select a.Ticket_Id, a.index_load , Createdate as RecordDate, a.Qty_Jobmix as Load_Size, d.Jobmix_Code, a.BP_ID, a.CreateBy, b.api, b.api_time_correction, b.bp_name, c.region_name
				from `TICKET` a
					left join `Batching_plant` b on a.BP_ID=b.id_bp
					left join `Region` c on b.id_region=c.id_region
					left join `JOBMIX_HEADER` d on a.Jobmix_Id=d.Jobmix_Id
				where a.Remarks = '" . $jo['JO_Number'] . "' " . $last_post_id . $apiupdate . " and b.api='live-bspi' order by index_load asc limit 5";

		echo '<div class="blue">' . $query . "</div>";
		$arrLoadJoNumber =  mysqli_query($con_ab, $query);


		$total_qty_load = 0;
		$first = true;
		$no_production = true;
		while ($load = mysqli_fetch_array($arrLoadJoNumber)) {
			$no_production = false;
			echo  '<br>Ticket_Id : ' . $load['Ticket_Id'] . '<br>';
			echo $jo['Material_Code'] . '<br>' . $jo['Material_Unit'] . '<br><br>';

			//SELECT MATERIAL PER LOADID
			$query = "select Material_Code as Item_Code, sum(Actual_Qty) as qty, Material_Uom from `BATCH_DETAIL` 
						  where Ticket_Id = '" . $load['Ticket_Id'] . "'
						  group by Material_Code
						  order by Material_Code ASC";
			$result = mysqli_query($con_ab, $query);

			// koreksi jam post
			$RecordDate = new DateTime($load['RecordDate']);
			if ($load['api_time_correction']) {
				$oldRecordDate = new DateTime($load['RecordDate']);

				$newRecordDate = new DateTime($load['RecordDate']);
				$newRecordDate->sub(new DateInterval('P1D'));

				$cekJoDate = new DateTime($jo['JO_Date']);

				$jam = new DateTime($oldRecordDate->format('H:i'));
				$begin = new DateTime('00:01');
				$end = new DateTime('07:30');

				if ($begin <= $jam && $jam <= $end) {
					if ($newRecordDate > $cekJoDate) {
						$RecordDate = $newRecordDate;
						echo '<br><br>api_time_correction ' . $oldRecordDate->format('Y-m-d') . ' => ' . $newRecordDate->format('Y-m-d') . '<br><br>';
					}
				}

				// $RecordDate = $RecordDate->format('Y-m-d H:i:s');
			}

			$material = [];
			$material_akumulasi = [];
			$kirim = true;
			$invalid = '';
			$validasi = array();

			foreach ($result as $materialvalue) {

				if (!in_array($materialvalue['Item_Code'], ['WATER', 'AIR', 'AIRR']) && strpos(strtolower($materialvalue['Item_Code']), 'water') === false && strpos(strtolower($materialvalue['Item_Code']), 'air') === false) {
					$amt = 0;

					//konversi mL ke L
					echo $materialvalue['Item_Code'] . ' - ' . $materialvalue['qty'] . ' ' . $materialvalue['Material_Uom'] . ' => ';

					//CEK Item COde Alias
					$item_code = $materialvalue['Item_Code'];
					if ($item_code[0] == '_') {
						$query_alias = "select Item_Code_ERP from Item_Code_Alias where Item_Code_BP = '" . $item_code . "'";
						$objAlias =  mysqli_query($con_ab, $query_alias);
						while ($alias = mysqli_fetch_array($objAlias)) {
							$materialvalue['Item_Code'] = $alias['Item_Code_ERP'];
							echo ' ' . $materialvalue['Item_Code'] . ' ';
						}
					}


					if ($materialvalue['Material_Uom'] == 'mL') {
						// $index_satuan = array_search($materialvalue['Item_Code'], $arrItems);

						//konversi amount item
						// if($index_satuan !== false){	
						$amt = $materialvalue['qty'] / 1000;
						echo $amt . ' L';
						// }else{
						// echo 'Item not found';
						// }
					}

					if ($materialvalue['Material_Uom'] != 'mL') {

						if ($materialvalue['Material_Uom'] == 'g') {
							$materialvalue['qty'] = $materialvalue['qty'] / 1000;
							$materialvalue['Material_Uom'] = 'kg';
						}

						$index_satuan = array_search($materialvalue['Item_Code'], $arrItems);

						//konversi amount item
						if (strtoupper($materialvalue['Material_Uom']) != $arrSatuanItems[$index_satuan] && $index_satuan !== false) {
							// $index_konversi = array_search($materialvalue['Item_Code'] ,array_column($arrKonversi, 'Item_Code'));
							$index_konversi = false;
							foreach ($arrKonversi as $keyKonversi => $valKonversi) {
								if ($valKonversi['Item_Code'] == $materialvalue['Item_Code'] && $valKonversi['Doc_Type'] == 'Production') {
									$index_konversi = $keyKonversi;
								}
							}

							// $amt = $arrKonversi[$index_konversi]['Item_Density'] * $materialvalue['qty'] / $arrKonversi[$index_konversi]['Scale1'];
							$amt = $index_konversi === false ? 'xxx' : $arrKonversi[$index_konversi]['Item_Density'] * $materialvalue['qty'] / $arrKonversi[$index_konversi]['Scale1'];
							$amt = $index_konversi === false ? 'xxx' : round($amt, 6);

							$kirim = $index_konversi === false ? false : true;
							$invalid = $index_konversi === false ? 'Item Conversion Not Found' : '';

							echo $index_konversi === false ? '<b>(Item Conversion Not Found)</b>' : $amt . ' ' . $arrSatuanItems[$index_satuan] . ' = ' . $arrKonversi[$index_konversi]['Item_Density'] . ' * ' . $materialvalue['qty'] . ' / ' . $arrKonversi[$index_konversi]['Scale1'];
						} elseif (strtoupper($materialvalue['Material_Uom']) == $arrSatuanItems[$index_satuan] && $index_satuan !== false) {
							$amt = $materialvalue['qty'];
							echo $amt . ' ' . $arrSatuanItems[$index_satuan];
						} else {
							echo '<b>(Invalid Item)</b>';
							$kirim = false;
							$invalid = 'INVALID';
						}
					}

					// validasi item kode sesuai dengan item ERP sebelum di post 
					$cek = array_search($materialvalue['Item_Code'], $arrItems);
					if (!empty($cek) || is_int($cek) == true) {
						echo '<b> ##PASS## </b>';
					} else {
						array_push($validasi, $cek);
						echo '<b> ##NOT FOUND## </b>';
					}

					echo '<br>';
					if (isset($material_akumulasi[$materialvalue['Item_Code']]) && $material_akumulasi[$materialvalue['Item_Code']] != 'xxx') {
						$material_akumulasi[$materialvalue['Item_Code']] = $material_akumulasi[$materialvalue['Item_Code']] + $amt;
					} else {
						$material_akumulasi[$materialvalue['Item_Code']] = $amt;
					}
					$material[$materialvalue['Item_Code']] = $amt;
				}
			}

			// generate string material asli
			$arr_material_str = [];
			foreach ($material as $x => $y) {
				$arr_material_str[] = $x . '|' . $y;
			}
			$material = implode('-', $arr_material_str);

			// generate sting material akumulasi
			$arr_material_akumulasi_str = [];
			foreach ($material_akumulasi as $x => $y) {
				$arr_material_akumulasi_str[] = $x . '|' . $y;
			}
			$material_akumulasi = implode('-', $arr_material_akumulasi_str);

			echo '<br>';

			if ($material != '' && $kirim == true && empty($validasi)) {

				$url = "http://192.168.100.17/apberp/erp/eaccounting/tools/api/sfservice.cfc?method=setDataBSPI&JO_Number=" . $jo['JO_Number'] . "&Reference=A" . $load['index_load'] . "&Trx_Date=" . $RecordDate->format("Y-m-d") . "&Ship_Distance=1&Output_Qty=" . $load['Load_Size'] . "&Input_Qty=" . $material_akumulasi;

				echo $url;
				echo "<br>";

				//  Initiate curl
				$ch = curl_init();
				// Will return the response, if false it print the response
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				// Set the url
				curl_setopt($ch, CURLOPT_URL, $url);
				// Execute
				$result = curl_exec($ch);
				// Closing
				curl_close($ch);

				// Will dump a beauty json :3
				$result = json_decode($result, true);
				echo "<br>";
				var_dump($result);
				echo "<br>";

				//POST SUKSES
				if ($result['return'] == 1) {
					echo "---------------------------------------------------<b class='blue'>POST</b>--<b class='blue'>SUKSES</b>----------------------------------------------------------------------<br><br>";

					$Created_Date = $jo['Created_Date'];
					$Last_Update = $jo['Last_Update'];

					$Created_Date = !empty($Created_Date) ? "'$Created_Date'" : "NULL";
					$Last_Update = !empty($Last_Update) ? "'$Last_Update'" : "NULL";

					$total_qty_load = $total_qty_load + $load['Load_Size'];
					//LOG GET
					if ($first) {
						$query = "INSERT INTO `API_Logs_Detail`(`type`, `Method`, `JO_Number`, `Jobmix_ERP`, `Req_Qty`, `Remain_Qty`, `CreateByERP`, `CreateDateERP`, `UpdateByERP`, `UpdateDateERP`, 
									 `Keterangan`, bp_id) VALUES ('live-bspi', 'GET', '" . $jo['JO_Number'] . "', '" . $jo['Item_Code'] . "', " . $jo['Item_Qty'] . ", " . $jo['Remaining_Qty'] . ", '" . $jo['Created_By'] . "', 
									" . $Created_Date . ", '" . $jo['Updated_By'] . "', " . $Last_Update . ", 'sukses', " . $load['BP_ID'] . ")";
						mysqli_query($con_ab, $query);
						$first = false;
					}

					//LOG POST
					$query = "INSERT INTO `API_Logs_Detail`(`type`, `Method`, `JO_Number`, `Jobmix_ERP`, `Jobmix_BP`, `Req_Qty`, `Remain_Qty`, `Post_Qty`, `BP`, `CreateByERP`, `CreateDateERP`, `UpdateByERP`, `UpdateDateERP`, CreateByBP, CreateDateBP, Regional,
								`Material`, `Keterangan`, `index_load`, Ticket_Id, bp_id) VALUES ('live-bspi', 'POST', '" . $jo['JO_Number'] . "', '" . $jo['Item_Code'] . "', '" . $load['Jobmix_Code'] . "', " . $jo['Item_Qty'] . ", " . ($jo['Remaining_Qty'] - $total_qty_load) . ", " . $load['Load_Size'] . ", '" . $load['bp_name'] . "', '" . $jo['Created_By'] . "', 
								" . $Created_Date . ", '" . $jo['Updated_By'] . "', " . $Last_Update . ", '" . $load['CreateBy'] . "', '" . $RecordDate->format("Y-m-d H:i:s") . "', '" . $load['region_name'] . "', '" . $material . "', 'sukses', " . $load['index_load'] . ", " . $load['Ticket_Id'] . "," . $load['BP_ID'] . ")";
					if (!mysqli_query($con_ab, $query)) {
						echo '<br>gagal insert (post suksek)<br>' . $query . '<br>';
					}
				} else {
					echo "---------------------------------------------------<b class='red'>POST</b>--<b class='red'>GAGAL</b>----------------------------------------------------------------------<br><br>";

					$query = "	INSERT INTO `API_Post_Gagal`(`type`, `JO_Number`, `Jobmix_ERP`, `Jobmix_BP`, `index_load`, Ticket_Id, `Load_Size`, `Material`, `Keterangan`, `RecordDate`, `jml_post`, bp_id) 
									VALUES ('live-bspi', '" . $jo['JO_Number'] . "', '" . $jo['Item_Code'] . "', '" . $load['Jobmix_Code'] . "', " . $load['index_load'] . ", " . $load['Ticket_Id'] . ", " . $load['Load_Size'] . ", '" . $material . "', '" . $result['return'] . "', '" . $RecordDate->format("Y-m-d H:i:s") . "', 1, " . $load['BP_ID'] . ")";
					mysqli_query($con_ab, $query);
				}
				sleep(1);
			} else {
				echo "---------------------------------------------------<b class='red'>INVALID</b>----------------------------------------------------------------------<br><br>";

				$query = "	INSERT INTO `API_Post_Gagal`(`type`, `JO_Number`, `Jobmix_ERP`, `Jobmix_BP`, `index_load`, Ticket_Id, `Load_Size`, `Material`, `Keterangan`, `RecordDate`, `jml_post`, bp_id) 
								VALUES ('live-bspi', '" . $jo['JO_Number'] . "', '" . $jo['Item_Code'] . "', '" . $load['Jobmix_Code'] . "', " . $load['index_load'] . ", " . $load['Ticket_Id'] . ", " . $load['Load_Size'] . ", '" . $material . "', '" . $invalid . "', '" . $RecordDate->format("Y-m-d H:i:s") . "', 1, " . $load['BP_ID'] . ")";
				mysqli_query($con_ab, $query);
			}
		}

		echo $no_production ? '===============EMPTY===============<br>' : '';



		$Created_Date = $jo['Created_Date'];
		$Last_Update = $jo['Last_Update'];
		$Estimated_TimeProduction = $jo['Estimated_TimeProduction'];
		$JO_Date = $jo['JO_Date'];

		$Created_Date = !empty($Created_Date) ? "'$Created_Date'" : "NULL";
		$Last_Update = !empty($Last_Update) ? "'$Last_Update'" : "NULL";
		$Estimated_TimeProduction = !empty($Estimated_TimeProduction) ? "'$Estimated_TimeProduction'" : "NULL";
		$JO_Date = !empty($JO_Date) ? "'$JO_Date'" : "NULL";


		$query = "DELETE FROM `API_Logs_Header` WHERE `type`='live-bspi' and JO_Number='" . $jo['JO_Number'] . "'";
		// echo '<br>' . $query;
		if (mysqli_query($conmysql, $query)) {
			echo 'delete API_Logs_Header suskes <br>';
		} else {
			echo 'delete API_Logs_Header gagal <br>';
		}
		// INSERT TEMP_GET
		$query = "INSERT INTO `API_Logs_Header`(`type`, `Created_By`, `Formula_Number`, `Item_Qty`, `JO_Number`, `SIP_Number`, `Created_Date`, `Item_Code`, `Item_Name`, `JO_Reference`,
			`Last_Update`, `MR_Number`, `Remaining_Qty`, `Updated_By`, `Estimated_TimeProduction`, `Material_Name`, `JO_Date`, `Material_Code`, API_Date) VALUES ('live-bspi', '" . $jo['Created_By'] . "', 
			'" . $jo['Formula_Number'] . "', " . $jo['Item_Qty'] . ", '" . $jo['JO_Number'] . "', '" . $jo['SIP_Number'] . "', " . $Created_Date . ", '" . $jo['Item_Code'] . "', '" . $jo['Item_Name'] . "', 
			'" . $jo['JO_Reference'] . "', " . $Last_Update . ", '" . $jo['MR_Number'] . "', " . ($jo['Remaining_Qty'] - $total_qty_load) . ", '" . $jo['Updated_By'] . "', " . $Estimated_TimeProduction . ", '" . $jo['Material_Name'] . "', 
			" . $JO_Date . ", '" . $jo['Material_Code'] . "', '" . date('Y-m-d H:i:s') . "')";
		// echo $query."<br>";

		if (mysqli_query($con_ab, $query)) {
			echo $jo['JO_Number'] . " SUKSES ditambahkan ke Temp_API<br>===================================<b class='blue'>HEADER</b>==<b class='blue'>SUKSES</b>=============================================================";
		} else {
			echo $jo['JO_Number'] . " GAGAL insert ke Temp_API<br>=====================================<b class='red'>HEADER</b>==<b class='red'>GAGAL</b>============================================================";
		}

		if ($jo['Remaining_Qty'] - $total_qty_load <= 0) {
			// INSERT JO FINISH
			$query = "INSERT INTO `API_Finish`(`type`,`Created_By`, `Formula_Number`, `Item_Qty`, `JO_Number`, `SIP_Number`, `Created_Date`, `Item_Code`, `Item_Name`, `JO_Reference`,
			`Last_Update`, `MR_Number`, `Remaining_Qty`, `Updated_By`, `Estimated_TimeProduction`, `Material_Name`, `JO_Date`, `Material_Code`) VALUES ('live-bspi','" . $jo['Created_By'] . "', 
			'" . $jo['Formula_Number'] . "', " . $jo['Item_Qty'] . ", '" . $jo['JO_Number'] . "', '" . $jo['SIP_Number'] . "', " . $Created_Date . ", '" . $jo['Item_Code'] . "', '" . $jo['Item_Name'] . "', 
			'" . $jo['JO_Reference'] . "', " . $Last_Update . ", '" . $jo['MR_Number'] . "', " . ($jo['Remaining_Qty'] - $total_qty_load) . ", '" . $jo['Updated_By'] . "', " . $Estimated_TimeProduction . ", '" . $jo['Material_Name'] . "', 
			" . $JO_Date . ", '" . $jo['Material_Code'] . "')";
			// echo $query."<br>";

			if (mysqli_query($con_ab, $query)) {
				echo $jo['JO_Number'] . " SUKSES ditambahkan ke API_Finish<br>===================================<b class='blue'>API_Finish</b>==<b class='blue'>SUKSES</b>=============================================================";
			} else {
				echo $jo['JO_Number'] . " GAGAL insert ke API_Finish<br>=====================================<b class='red'>API_Finish</b>==<b class='red'>GAGAL</b>============================================================";
			}
		}


		echo "<br><br><br>";
	}
	$query = "DELETE FROM `API_Logs_Header` WHERE `type` = 'live-bspi' and API_Date<'$timestamp'";
	mysqli_query($con_ab, $query);
} else {
	echo '<b class="red">ERP KOSONG</b>';
}

?>