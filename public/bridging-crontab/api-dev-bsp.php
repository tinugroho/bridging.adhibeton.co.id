<style>
	.red {
		color: red;
	}

	.blue {
		color: blue;
	}
</style>
<?php

class Api
{
	var $conmysql;

	public function __construct()
	{
		require_once "koneksi.php";
		$this->conmysql = $conmysql;
	}

	public function getDataBSP()
	{
		$url = "http://192.168.100.17/apberpdev/erp/eaccounting/tools/api/sfservice.cfc?method=getDataBSP2";
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

		if (isset($result['databsp2'])) {
			echo 'getDataBSP2 suskes <br>';
		} else {
			echo 'getDataBSP2 gagal <br>';
		}
		return $result;
	}

	public function getDataDensity()
	{
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

		if (isset($resultKonversi['dataitem'])) {
			echo 'getDataDensity suskes <br>';
		} else {
			echo 'getDataDensity gagal <br>';
		}
		return $arrKonversi;
	}

	public function deleteApiLogHeader($timestamp)
	{
		$query = "DELETE FROM `API_Logs_Header` WHERE `type`='dev-bsp' and API_Date<'$timestamp'";
		if (mysqli_query($this->conmysql, $query)) {
			echo 'delete API_Logs_Header suskes <br>';
		} else {
			echo 'delete API_Logs_Header gagal <br>';
		}
	}
	public function deleteApiLogHeaderPerJo($JO_Number)
	{
		$query = "DELETE FROM `API_Logs_Header` WHERE `type`='dev-bsp' and JO_Number='$JO_Number'";
		if (mysqli_query($this->conmysql, $query)) {
			echo 'delete API_Logs_Header suskes <br>';
		} else {
			echo 'delete API_Logs_Header gagal <br>';
		}
	}

	public function last_post_id($JO_Number)
	{
		//CARI ID LAST POST JO NUMBER
		$query = "	select Index_Load from API_Logs_Detail where Method = 'POST' and JO_Number = '" . $JO_Number . "' and `type`='dev-bsp' 
					union
					(select Index_Load from API_Post_Gagal where JO_Number = '" . $JO_Number . "' and `type`='dev-bsp')
					order by Index_Load desc limit 1";
		$last_post = mysqli_query($this->conmysql, $query);

		$last_post_id = "";
		while ($row = mysqli_fetch_array($last_post)) {
			$last_post_id = " and index_load > " . $row['Index_Load'] . " ";
		}

		echo 'last_post_id = ' . $last_post_id . "<br>";
		return $last_post_id;
	}

	public function loadJoNumber($JO_Number)
	{
		$last_post_id = $this->last_post_id($JO_Number);
		$apiupdate = " and a.RecordDate > '2021-06-08 13:06'";

		//SELECT LOAD JO NUMBER
		$query = "select max(a.index_load) as index_load ,a.LoadID, max(a.RecordDate) as RecordDate, a.Load_Size, a.Item_Code, a.BP_ID, a.CreatedBy, a.RecordDate, a.Ticket_Code, a.Truck_Code, a.Driver_Code, b.bp_name, b.api_time_correction, c.region_name
				from `V_BatchSetupTickets` a
					left join `Batching_plant` b on a.BP_ID=b.id_bp
					left join `Region` c on b.id_region=c.id_region
				where a.Delivery_Instruction = '" . $JO_Number . "' " . $last_post_id . $apiupdate . " and a.Ticket_Status='O' and b.api='dev-bsp' group by LoadID order by index_load asc limit 5";

		$result =  mysqli_query($this->conmysql, $query);

		$arrLoadJoNumber = array();
		while ($load = mysqli_fetch_array($result)) {
			$arrLoadJoNumber[] = $load;
		}


		echo COUNT($arrLoadJoNumber) . ' LOAD JO NUMBER DITEMUKAN<br>';

		return $arrLoadJoNumber;
	}

	public function materialPerLoad($LoadID)
	{
		//SELECT MATERIAL PER LOADID
		$query = "select Item_Code, sum(Net_Auto_Batched_Amt) as Auto, Amt_UOM from `Load_Lines` 
		where LoadID = '" . $LoadID . "'
		group by Item_Code
		order by Sort_Line_Num ASC";
		$result = mysqli_query($this->conmysql, $query);

		$materialPerLoad = array();
		while ($material = mysqli_fetch_array($result)) {
			$materialPerLoad[] = $material;
		}
		return $materialPerLoad;
	}
	public function itemAlias($item_code)
	{
		$query_alias = "select Item_Code_ERP from Item_Code_Alias where Item_Code_BP = '" . $item_code . "'";
		$objAlias =  mysqli_query($this->conmysql, $query_alias);
		while ($alias = mysqli_fetch_array($objAlias)) {
			return $alias;
		}
	}

	public function setDataBSP($JO_Number, $SO_Number, $index_load, $Load_Size, $material, $Driver_Code, $Truck_Code, $Ticket_Code, $RecordDate)
	{
		$url = 	"http://192.168.100.17/apberpdev/erp/eaccounting/tools/api/sfservice.cfc?method=setDataBSP&JO_Number=" . $JO_Number . "&SO_Number=" . $SO_Number . "&Reference=" . $index_load . "&Trx_Date=" . $RecordDate->format('Y-m-d') . "&Ship_Distance=1&Output_Qty=" . $Load_Size . "&Input_Qty=" . $material . "&Driver=" . $Driver_Code . "&Vehicle_No=" . $Truck_Code . "&Trx_ID=" . $Ticket_Code;

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
		return $result;
	}

	public function logGet($JO_Number, $Item_Code, $Item_Qty, $Remaining_Qty, $Created_By, $Created_Date, $Updated_By, $Last_Update)
	{
		$query = "INSERT INTO `API_Logs_Detail`(`type`, `Method`, `JO_Number`, `Jobmix_ERP`, `Req_Qty`, `Remain_Qty`, `CreateByERP`, `CreateDateERP`, `UpdateByERP`, `UpdateDateERP`, 
								`Keterangan`) VALUES ('dev-bsp', 'GET', '" . $JO_Number . "', '" . $Item_Code . "', " . $Item_Qty . ", " . $Remaining_Qty . ", '" . $Created_By . "', 
								" . $Created_Date . ", '" . $Updated_By . "', " . $Last_Update . ", 'sukses')";
		if (mysqli_query($this->conmysql, $query)) {
			echo 'INSERT GET INTO API_Logs_Detail sukses <br>';
			return true;
		} else {
			echo 'Log API GET gagal <br>';
		}
	}

	public function logPost($JO_Number, $Jo_Item_Code, $Load_Item_Code, $Item_Qty, $Remaining_Qty, $total_qty_load, $Load_Size, $bp_name, $Created_By,  $Created_Date, $Updated_By, $Last_Update, $CreatedBy, $RecordDate, $region_name, $index_load, $material)
	{
		$query = "INSERT INTO `API_Logs_Detail`(`type`, `Method`, `JO_Number`, `Jobmix_ERP`, `Jobmix_BP`, `Req_Qty`, `Remain_Qty`, `Post_Qty`, `BP`, `CreateByERP`, `CreateDateERP`, `UpdateByERP`, `UpdateDateERP`, CreateByBP, CreateDateBP, Regional,
								`Material`, `Keterangan`, `Index_Load`) VALUES ('dev-bsp', 'POST', '" . $JO_Number . "', '" . $Jo_Item_Code . "', '" . $Load_Item_Code . "', " . $Item_Qty . ", " . ($Remaining_Qty - $total_qty_load) . ", " . $Load_Size . ", '" . $bp_name . "', '" . $Created_By . "', 
								" . $Created_Date . ", '" . $Updated_By . "', " . $Last_Update . ", '" . $CreatedBy . "', '" . $RecordDate->format('Y-m-d H:i:s') . "', '" . $region_name . "', '" . $material . "', 'sukses', " . $index_load . ")";
		if (mysqli_query($this->conmysql, $query)) {

			echo 'INSERT POST INTO API_Logs_Detail sukses <br>';
			return true;
		} else {
			echo 'Log API POST gagal <br>' . $query . '<br>';
		}
	}

	public function logGagal($JO_Number, $Item_Code_ERP, $Item_Code_BP, $index_load, $Load_Size, $material, $SO_Number, $Ticket_Code, $Driver_Code, $Truck_Code, $keterangan, $RecordDate, $jml_post)
	{
		$query = "	INSERT INTO `API_Post_Gagal`(`type`, `JO_Number`, `Jobmix_ERP`, `Jobmix_BP`, `Index_Load`, `Load_Size`, `Material`, `SO_Number`, `Ticket_Code`, `Driver_Code`, `Truck_Code`,  `Keterangan`, `RecordDate`, `jml_post`) 
									VALUES ('dev-bsp', '" . $JO_Number . "', '" . $Item_Code_ERP . "', '" . $Item_Code_BP . "', " . $index_load . ", " . $Load_Size . ", '" . $material . "', '$SO_Number', $Ticket_Code, '$Driver_Code', $Truck_Code, '" . $keterangan . "', '" . $RecordDate->format('Y-m-d H:i:s') . "', $jml_post)";
		if (mysqli_query($this->conmysql, $query)) {

			echo 'INSERT GAGAL INTO API_Post_Gagal sukses <br>';
			return true;
		} else {
			echo 'Log API GAGAL gagal <br>';
			echo $query . '<br>';
		}
	}

	public function insertApiHeader($Created_By, $Formula_Number, $Item_Qty, $JO_Number, $SIP_Number, $Created_Date, $Item_Code, $Item_Name, $JO_Reference, $Last_Update, $MR_Number, $Remaining_Qty, $total_qty_load, $Updated_By, $Estimated_TimeProduction, $Material_Name, $JO_Date, $Material_Code)
	{
		$query = "INSERT INTO `API_Logs_Header`(`type`, `Created_By`, `Formula_Number`, `Item_Qty`, `JO_Number`, `SIP_Number`, `Created_Date`, `Item_Code`, `Item_Name`, `JO_Reference`,
			`Last_Update`, `MR_Number`, `Remaining_Qty`, `Updated_By`, `Estimated_TimeProduction`, `Material_Name`, `JO_Date`, `Material_Code`, API_Date) VALUES ('dev-bsp', '" . $Created_By . "', 
			'" . $Formula_Number . "', " . $Item_Qty . ", '" . $JO_Number . "', '" . $SIP_Number . "', " . $Created_Date . ", '" . $Item_Code . "', '" . $Item_Name . "', 
			'" . $JO_Reference . "', " . $Last_Update . ", '" . $MR_Number . "', " . ($Remaining_Qty - $total_qty_load) . ", '" . $Updated_By . "', " . $Estimated_TimeProduction . ", '" . $Material_Name . "', 
			" . $JO_Date . ", '" . $Material_Code . "', '" . date('Y-m-d H:i:s') . "')";

		if (mysqli_query($this->conmysql, $query)) {
			echo 'INSERT INTO API_Logs_Header sukses <br>';
			return true;
		} else {
			echo 'INSERT API HEADER gagal <br>';
		}
	}

	public function insertApiFinish($Created_By, $Formula_Number, $Item_Qty, $JO_Number, $SIP_Number, $Created_Date, $Item_Code, $Item_Name, $JO_Reference, $Last_Update, $MR_Number, $Remaining_Qty, $total_qty_load, $Updated_By, $Estimated_TimeProduction, $Material_Name, $JO_Date, $Material_Code)
	{
		$query = "INSERT INTO `API_Finish`(`type`, `Created_By`, `Formula_Number`, `Item_Qty`, `JO_Number`, `SIP_Number`, `Created_Date`, `Item_Code`, `Item_Name`, `JO_Reference`,
			`Last_Update`, `MR_Number`, `Remaining_Qty`, `Updated_By`, `Estimated_TimeProduction`, `Material_Name`, `JO_Date`, `Material_Code`) VALUES ('dev-bsp', '" . $Created_By . "', 
			'" . $Formula_Number . "', " . $Item_Qty . ", '" . $JO_Number . "', '" . $SIP_Number . "', " . $Created_Date . ", '" . $Item_Code . "', '" . $Item_Name . "', 
			'" . $JO_Reference . "', " . $Last_Update . ", '" . $MR_Number . "', " . ($Remaining_Qty - $total_qty_load) . ", '" . $Updated_By . "', " . $Estimated_TimeProduction . ", '" . $Material_Name . "', 
			" . $JO_Date . ", '" . $Material_Code . "')";

		if (mysqli_query($this->conmysql, $query)) {

			echo 'INSERT INTO API_Finish sukses <br>';
			return true;
		} else {
			echo 'INSERT API FINISH gagal <br>';
		}
	}
}


/* set default timezone */
date_default_timezone_set("Asia/Jakarta");
$timestamp = date('Y-m-d H:m:s');


$api = new Api();
$conmysql = $api->conmysql;
$databsp = $api->getDataBSP();


//kosongkan tabel
if (!empty($databsp['databsp2'])) {


	$arrKonversi = $api->getDataDensity();


	//isi ulang tabel
	foreach ($databsp['databsp2'] as $jo) {
		if ($jo['Last_Update'] == '') {
			$jo['Last_Update'] = '2019-01-01';
		}

		//ARRAY URUTAN ITEMS
		$arrItems = explode('|', $jo['Material_Code']);

		//ARRAY SATUAN ITEMS
		$arrSatuanItems = explode('|', $jo['Material_Unit']);

		// ARRAY LOAD
		$arrLoadJoNumber = $api->loadJoNumber($jo['JO_Number']);

		// PENGGANTI INSERT NULL
		$Created_Date = !empty($jo['Created_Date']) ? "'" . $jo['Created_Date'] . "'" : "NULL";
		$Last_Update = !empty($jo['Last_Update']) ? "'" . $jo['Last_Update'] . "'" : "NULL";
		$Estimated_TimeProduction = !empty($jo['Estimated_TimeProduction']) ? "'" . $jo['Estimated_TimeProduction'] . "'" : "NULL";
		$JO_Date = !empty($jo['JO_Date']) ? "'" . $jo['JO_Date'] . "'" : "NULL";

		$total_qty_load = 0;
		$first = true;
		// PER LOAD
		foreach ($arrLoadJoNumber as $load) {

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
						$RecordDate =  $newRecordDate;
						echo '<br><br>api_time_correction ' . $oldRecordDate->format('Y-m-d') . ' => ' . $newRecordDate->format('Y-m-d') . '<br><br>';
					}
				}

				// $RecordDate = $RecordDate->format('Y-m-d H:i:s');
			}

			echo $load['LoadID'] . "<br>";
			echo $jo['Material_Code'] . '<br>' . $jo['Material_Unit'] . '<br><br>';

			$materialPerLoad = $api->materialPerLoad($load['LoadID']);

			$material = [];
			$material_akumulasi = [];
			$invalid = '';
			$kirim = true;
			foreach ($materialPerLoad as $materialvalue) {

				if (!in_array($materialvalue['Item_Code'], ['WATER', 'AIR', 'AIRR']) && strpos(strtolower($materialvalue['Item_Code']), 'water') === false && strpos(strtolower($materialvalue['Item_Code']), 'air') === false) {
					$amt = 0;

					echo $materialvalue['Item_Code'] . ' - ' . $materialvalue['Auto'] . ' ' . $materialvalue['Amt_UOM'] . ' => ';

					//CEK Item COde Alias
					$item_code = $materialvalue['Item_Code'];
					if ($item_code[0] == '_') {
						$alias = $api->itemAlias($item_code);

						$materialvalue['Item_Code'] = $alias['Item_Code_ERP'];
						echo ' ' . $materialvalue['Item_Code'] . ' ';
					}

					// KONVERSI
					if ($materialvalue['Amt_UOM'] == 'mL') {
						$amt = $materialvalue['Auto'] / 1000;
						echo $amt . ' L';
					} else {

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

				$setDataBSP = $api->setDataBSP($jo['JO_Number'], $jo['SO_Number'], $load['index_load'], $load['Load_Size'], $material_akumulasi, $load['Driver_Code'], $load['Truck_Code'], $load['Ticket_Code'], $RecordDate);

				var_dump($setDataBSP);
				echo "<br>";

				//POST SUKSES
				if ($setDataBSP['return'] == 1) {
					echo "---------------------------------------------------<b class='blue'>POST</b>--<b class='blue'>SUKSES</b>----------------------------------------------------------------------<br><br>";

					$total_qty_load = $total_qty_load + $load['Load_Size'];

					//LOG GET
					if ($first) {
						$api->logGet($jo['JO_Number'], $jo['Item_Code'], $jo['Item_Qty'], $jo['Remaining_Qty'], $jo['Created_By'], $Created_Date, $jo['Updated_By'], $Last_Update);
						$first = false;
					}

					//LOG POST
					$api->logPost($jo['JO_Number'], $jo['Item_Code'], $load['Item_Code'], $jo['Item_Qty'], $jo['Remaining_Qty'], $total_qty_load, $load['Load_Size'], $load['bp_name'], $jo['Created_By'],  $Created_Date, $jo['Updated_By'], $Last_Update, $load['CreatedBy'], $RecordDate, $load['region_name'], $load['index_load'], $material);
				} else {
					echo "---------------------------------------------------<b class='red'>POST</b>--<b class='red'>GAGAL</b>----------------------------------------------------------------------<br><br>";

					// LOG POST GAGAL
					$api->logGagal($jo['JO_Number'], $jo['Item_Code'], $load['Item_Code'], $load['index_load'],  $load['Load_Size'], $material, $jo['SO_Number'], $load['Ticket_Code'], $load['Driver_Code'], $load['Truck_Code'], $setDataBSP['return'], $RecordDate, 1);
				}
				sleep(1);
			} else {
				echo "---------------------------------------------------<b class='red'>INVALID</b>----------------------------------------------------------------------<br><br>";

				// LOG POST INVALID
				$api->logGagal($jo['JO_Number'], $jo['Item_Code'], $load['Item_Code'], $load['index_load'],  $load['Load_Size'], $material, $jo['SO_Number'], $load['Ticket_Code'], $load['Driver_Code'], $load['Truck_Code'], $invalid, $RecordDate, 0);
			}
		}

		// INSERT TEMP_GET
		$api->deleteApiLogHeaderPerJo($jo['JO_Number']);
		$insertApiHeader = $api->insertApiHeader($jo['Created_By'], $jo['Formula_Number'], $jo['Item_Qty'], $jo['JO_Number'], $jo['SIP_Number'], $Created_Date, $jo['Item_Code'], $jo['Item_Name'], $jo['JO_Reference'], $Last_Update, $jo['MR_Number'], $jo['Remaining_Qty'], $total_qty_load, $jo['Updated_By'], $Estimated_TimeProduction, $jo['Material_Name'], $JO_Date, $jo['Material_Code']);
		if ($insertApiHeader) {
			echo $jo['JO_Number'] . " SUKSES ditambahkan ke Temp_API<br>===================================<b class='blue'>HEADER</b>==<b class='blue'>SUKSES</b>=============================================================";
		} else {
			echo $jo['JO_Number'] . " GAGAL insert ke Temp_API<br>=====================================<b class='red'>HEADER</b>==<b class='red'>GAGAL</b>============================================================";
		}

		if ($jo['Remaining_Qty'] - $total_qty_load <= 0) {

			// INSERT JO FINISH
			$insertApiFinish = $api->insertApiFinish($jo['Created_By'], $jo['Formula_Number'], $jo['Item_Qty'], $jo['JO_Number'], $jo['SIP_Number'], $Created_Date, $jo['Item_Code'], $jo['Item_Name'], $jo['JO_Reference'], $Last_Update, $jo['MR_Number'], $jo['Remaining_Qty'], $total_qty_load, $jo['Updated_By'], $Estimated_TimeProduction, $jo['Material_Name'], $JO_Date, $jo['Material_Code']);
			if ($insertApiFinish) {
				echo $jo['JO_Number'] . " SUKSES ditambahkan ke API_Finish<br>===================================<b class='blue'>API_Finish</b>==<b class='blue'>SUKSES</b>=============================================================";
			} else {
				echo $jo['JO_Number'] . " GAGAL insert ke API_Finish<br>=====================================<b class='red'>API_Finish</b>==<b class='red'>GAGAL</b>============================================================";
			}
		}

		echo "<br><br><br>";
	}

	$api->deleteApiLogHeader($timestamp);
} else {
	echo '<b class="red">ERP KOSONG</b>';
}

?>