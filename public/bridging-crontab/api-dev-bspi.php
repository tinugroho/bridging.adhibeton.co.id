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


$url = "http://192.168.100.17/apberpdev/erp/eaccounting/tools/api/sfservice.cfc?method=getDataBSPI";
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
	$urlKonversi = "http://192.168.100.17/apberpdev/erp/eaccounting/tools/api/sfservice.cfc?method=getDataDensity";
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
		$query = "	select Index_Load from API_Logs_Detail where Method = 'POST' and JO_Number = '" . $jo['JO_Number'] . "' and `type` = 'dev-bspi' 
					union
					(select Index_Load from API_Post_Gagal where JO_Number = '" . $jo['JO_Number'] . "' and `type` = 'dev-bspi')
					order by Index_Load desc limit 1";
		echo $query . "<br>";
		$last_post = mysqli_query($conmysql, $query);

		$last_post_id = "";
		while ($row = mysqli_fetch_array($last_post)) {
			$last_post_id = " and index_load > " . $row['Index_Load'] . " ";
		}

		$apiupdate = " and a.RecordDate > '2021-06-08 13:06'";

		//SELECT LOAD JO NUMBER
		$query = "select max(a.index_load) as index_load ,a.LoadID, max(a.RecordDate) as RecordDate, a.Load_Size, a.Item_Code, a.BP_ID, a.CreatedBy, b.api, b.api_time_correction, b.bp_name, c.region_name
				from `V_BatchSetupTickets` a
					left join `Batching_plant` b on a.BP_ID=b.id_bp
					left join `Region` c on b.id_region=c.id_region
				where a.Delivery_Instruction = '" . $jo['JO_Number'] . "' " . $last_post_id . $apiupdate . " and a.Ticket_Status='O' and b.api='dev-bspi' group by LoadID order by index_load asc limit 5";

		echo '<div class="blue">' . $query . "</div><br><br>";
		$arrLoadJoNumber =  mysqli_query($conmysql, $query);


		// if(!empty($arrLoadJoNumber)){	
		$total_qty_load = 0;
		$first = true;
		while ($load = mysqli_fetch_array($arrLoadJoNumber)) {
			echo $load['LoadID'] . "<br>";
			echo $jo['Material_Code'] . '<br>' . $jo['Material_Unit'] . '<br><br>';

			//SELECT MATERIAL PER LOADID
			$query = "select Item_Code, sum(Net_Auto_Batched_Amt) as Auto, Amt_UOM from `Load_Lines` 
						  where LoadID = '" . $load['LoadID'] . "'
						  group by Item_Code
						  order by Sort_Line_Num ASC";
			$result = mysqli_query($conmysql, $query);

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
			foreach ($result as $materialvalue) {


				if (!in_array($materialvalue['Item_Code'], ['WATER', 'AIR', 'AIRR'])) {
					$amt = 0;

					//konversi mL ke L
					echo $materialvalue['Item_Code'] . ' - ' . $materialvalue['Auto'] . ' ' . $materialvalue['Amt_UOM'] . ' => ';

					//CEK Item COde Alias
					$item_code = $materialvalue['Item_Code'];
					if ($item_code[0] == '_') {
						$query_alias = "select Item_Code_ERP from Item_Code_Alias where Item_Code_BP = '" . $item_code . "'";
						$objAlias =  mysqli_query($conmysql, $query_alias);
						while ($alias = mysqli_fetch_array($objAlias)) {
							$materialvalue['Item_Code'] = $alias['Item_Code_ERP'];
							echo ' ' . $materialvalue['Item_Code'] . ' ';
						}
					}


					if ($materialvalue['Amt_UOM'] == 'mL') {
						// $index_satuan = array_search($materialvalue['Item_Code'], $arrItems);

						//konversi amount item
						// if($index_satuan !== false){	
						$amt = $materialvalue['Auto'] / 1000;
						echo $amt . ' L';
						// }else{
						// echo 'Item not found';
						// }
					}

					if ($materialvalue['Amt_UOM'] != 'mL') {

						if ($materialvalue['Amt_UOM'] == 'g') {
							$materialvalue['Auto'] = $materialvalue['Auto'] / 1000;
							$materialvalue['Amt_UOM'] = 'kg';
						}

						$index_satuan = array_search($materialvalue['Item_Code'], $arrItems);

						//konversi amount item
						if (strtoupper($materialvalue['Amt_UOM']) != $arrSatuanItems[$index_satuan] && $index_satuan !== false) {
							// $index_konversi = array_search($materialvalue['Item_Code'] ,array_column($arrKonversi, 'Item_Code'));
							$index_konversi = false;
							foreach ($arrKonversi as $keyKonversi => $valKonversi) {
								if ($valKonversi['Item_Code'] == $materialvalue['Item_Code'] && $valKonversi['Doc_Type'] == 'Production') {
									$index_konversi = $keyKonversi;
								}
							}

							// $amt = $arrKonversi[$index_konversi]['Item_Density'] * $materialvalue['Auto'] / $arrKonversi[$index_konversi]['Scale1'];
							$amt = $index_konversi === false ? 'xxx' : $arrKonversi[$index_konversi]['Item_Density'] * $materialvalue['Auto'] / $arrKonversi[$index_konversi]['Scale1'];
							$amt = $index_konversi === false ? 'xxx' : round($amt, 6);

							$kirim = $index_konversi === false ? false : true;
							$invalid = $index_konversi === false ? 'Item Conversion Not Found' : '';

							echo $index_konversi === false ? '<b>(Item Conversion Not Found)</b>' : $amt . ' ' . $arrSatuanItems[$index_satuan] . ' = ' . $arrKonversi[$index_konversi]['Item_Density'] . ' * ' . $materialvalue['Auto'] . ' / ' . $arrKonversi[$index_konversi]['Scale1'];
						} elseif (strtoupper($materialvalue['Amt_UOM']) == $arrSatuanItems[$index_satuan] && $index_satuan !== false) {
							$amt = $materialvalue['Auto'];
							echo $amt . ' ' . $arrSatuanItems[$index_satuan];
						} else {
							echo '<b>(Invalid Item)</b>';
							$kirim = false;
							$invalid = 'INVALID';
						}
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

			if ($material != '' && $kirim == true) {

				$url = "http://192.168.100.17/apberpdev/erp/eaccounting/tools/api/sfservice.cfc?method=setDataBSPI&JO_Number=" . $jo['JO_Number'] . "&Reference=" . $load['index_load'] . "&Trx_Date=" . $RecordDate->format("Y-m-d") . "&Ship_Distance=1&Output_Qty=" . $load['Load_Size'] . "&Input_Qty=" . $material_akumulasi;

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
					$query = "INSERT INTO `API_Logs_Detail`(`type`, `Method`, `JO_Number`, `Jobmix_ERP`, `Req_Qty`, `Remain_Qty`, `CreateByERP`, `CreateDateERP`, `UpdateByERP`, `UpdateDateERP`, 
								 `Keterangan`) VALUES ('dev-bspi', 'GET', '" . $jo['JO_Number'] . "', '" . $jo['Item_Code'] . "', " . $jo['Item_Qty'] . ", " . $jo['Remaining_Qty'] . ", '" . $jo['Created_By'] . "', 
								" . $Created_Date . ", '" . $jo['Updated_By'] . "', " . $Last_Update . ", 'sukses')";
					if ($first) {
						mysqli_query($conmysql, $query);
						$first = false;
					}

					//LOG POST
					$query = "INSERT INTO `API_Logs_Detail`(`type`, `Method`, `JO_Number`, `Jobmix_ERP`, `Jobmix_BP`, `Req_Qty`, `Remain_Qty`, `Post_Qty`, `BP`, `CreateByERP`, `CreateDateERP`, `UpdateByERP`, `UpdateDateERP`, CreateByBP, CreateDateBP, Regional,
								`Material`, `Keterangan`, `Index_Load`) VALUES ('dev-bspi', 'POST', '" . $jo['JO_Number'] . "', '" . $jo['Item_Code'] . "', '" . $load['Item_Code'] . "', " . $jo['Item_Qty'] . ", " . ($jo['Remaining_Qty'] - $total_qty_load) . ", " . $load['Load_Size'] . ", '" . $load['bp_name'] . "', '" . $jo['Created_By'] . "', 
								" . $Created_Date . ", '" . $jo['Updated_By'] . "', " . $Last_Update . ", '" . $load['CreatedBy'] . "', '" . $RecordDate->format("Y-m-d H:i:s") . "', '" . $load['region_name'] . "', '" . $material . "', 'sukses', " . $load['index_load'] . ")";
					mysqli_query($conmysql, $query);
				} else {
					echo "---------------------------------------------------<b class='red'>POST</b>--<b class='red'>GAGAL</b>----------------------------------------------------------------------<br><br>";

					$query = "	INSERT INTO `API_Post_Gagal`(`type`, `JO_Number`, `Jobmix_ERP`, `Jobmix_BP`, `Index_Load`, `Load_Size`, `Material`, `Keterangan`, `RecordDate`, `jml_post`) 
									VALUES ('dev-bspi', '" . $jo['JO_Number'] . "', '" . $jo['Item_Code'] . "', '" . $load['Item_Code'] . "', " . $load['index_load'] . ", " . $load['Load_Size'] . ", '" . $material . "', '" . $result['return'] . "', '" . $RecordDate->format("Y-m-d H:i:s") . "', 1)";
					mysqli_query($conmysql, $query);
				}
				sleep(1);
			} else {
				echo "---------------------------------------------------<b class='red'>INVALID</b>----------------------------------------------------------------------<br><br>";

				$query = "	INSERT INTO `API_Post_Gagal`(`type`, `JO_Number`, `Jobmix_ERP`, `Jobmix_BP`, `Index_Load`, `Load_Size`, `Material`, `Keterangan`, `RecordDate`, `jml_post`) 
								VALUES ('dev-bspi', '" . $jo['JO_Number'] . "', '" . $jo['Item_Code'] . "', '" . $load['Item_Code'] . "', " . $load['index_load'] . ", " . $load['Load_Size'] . ", '" . $material . "', '" . $invalid . "', '" . $RecordDate->format("Y-m-d H:i:s") . "', 1)";
				mysqli_query($conmysql, $query);
			}
		}
		// }



		$Created_Date = $jo['Created_Date'];
		$Last_Update = $jo['Last_Update'];
		$Estimated_TimeProduction = $jo['Estimated_TimeProduction'];
		$JO_Date = $jo['JO_Date'];

		$Created_Date = !empty($Created_Date) ? "'$Created_Date'" : "NULL";
		$Last_Update = !empty($Last_Update) ? "'$Last_Update'" : "NULL";
		$Estimated_TimeProduction = !empty($Estimated_TimeProduction) ? "'$Estimated_TimeProduction'" : "NULL";
		$JO_Date = !empty($JO_Date) ? "'$JO_Date'" : "NULL";


		// INSERT TEMP_GET
		$query = "DELETE FROM `API_Logs_Header` WHERE `type`='dev-bspi' and JO_Number='" . $jo['JO_Number'] . "'";
		mysqli_query($conmysql, $query);

		$query = "INSERT INTO `API_Logs_Header`(`type`, `Created_By`, `Formula_Number`, `Item_Qty`, `JO_Number`, `SIP_Number`, `Created_Date`, `Item_Code`, `Item_Name`, `JO_Reference`,
			`Last_Update`, `MR_Number`, `Remaining_Qty`, `Updated_By`, `Estimated_TimeProduction`, `Material_Name`, `JO_Date`, `Material_Code`, API_Date) VALUES ('dev-bspi', '" . $jo['Created_By'] . "', 
			'" . $jo['Formula_Number'] . "', " . $jo['Item_Qty'] . ", '" . $jo['JO_Number'] . "', '" . $jo['SIP_Number'] . "', " . $Created_Date . ", '" . $jo['Item_Code'] . "', '" . $jo['Item_Name'] . "', 
			'" . $jo['JO_Reference'] . "', " . $Last_Update . ", '" . $jo['MR_Number'] . "', " . ($jo['Remaining_Qty'] - $total_qty_load) . ", '" . $jo['Updated_By'] . "', " . $Estimated_TimeProduction . ", '" . $jo['Material_Name'] . "', 
			" . $JO_Date . ", '" . $jo['Material_Code'] . "', '" . date('Y-m-d H:i:s') . "')";
		// echo $query."<br>";

		if (mysqli_query($conmysql, $query)) {
			echo $jo['JO_Number'] . " SUKSES ditambahkan ke Temp_API<br>===================================<b class='blue'>HEADER</b>==<b class='blue'>SUKSES</b>=============================================================";
		} else {
			echo $jo['JO_Number'] . " GAGAL insert ke Temp_API<br>=====================================<b class='red'>HEADER</b>==<b class='red'>GAGAL</b>============================================================";
		}

		if ($jo['Remaining_Qty'] - $total_qty_load <= 0) {
			// INSERT JO FINISH
			$query = "INSERT INTO `API_Finish`(`type`,`Created_By`, `Formula_Number`, `Item_Qty`, `JO_Number`, `SIP_Number`, `Created_Date`, `Item_Code`, `Item_Name`, `JO_Reference`,
			`Last_Update`, `MR_Number`, `Remaining_Qty`, `Updated_By`, `Estimated_TimeProduction`, `Material_Name`, `JO_Date`, `Material_Code`) VALUES ('dev-bspi','" . $jo['Created_By'] . "', 
			'" . $jo['Formula_Number'] . "', " . $jo['Item_Qty'] . ", '" . $jo['JO_Number'] . "', '" . $jo['SIP_Number'] . "', " . $Created_Date . ", '" . $jo['Item_Code'] . "', '" . $jo['Item_Name'] . "', 
			'" . $jo['JO_Reference'] . "', " . $Last_Update . ", '" . $jo['MR_Number'] . "', " . ($jo['Remaining_Qty'] - $total_qty_load) . ", '" . $jo['Updated_By'] . "', " . $Estimated_TimeProduction . ", '" . $jo['Material_Name'] . "', 
			" . $JO_Date . ", '" . $jo['Material_Code'] . "')";
			// echo $query."<br>";

			if (mysqli_query($conmysql, $query)) {
				echo $jo['JO_Number'] . " SUKSES ditambahkan ke API_Finish<br>===================================<b class='blue'>API_Finish</b>==<b class='blue'>SUKSES</b>=============================================================";
			} else {
				echo $jo['JO_Number'] . " GAGAL insert ke API_Finish<br>=====================================<b class='red'>API_Finish</b>==<b class='red'>GAGAL</b>============================================================";
			}
		}


		echo "<br><br><br>";
	}
	$query = "DELETE FROM `API_Logs_Header` WHERE `type` = 'dev-bspi' and API_Date<'$timestamp'";
	mysqli_query($conmysql, $query);
} else {
	echo '<b class="red">ERP KOSONG</b>';
}

?>