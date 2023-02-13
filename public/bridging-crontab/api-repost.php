<?php
class Api_repost
{
	var $conmysql;
	public function __construct()
	{
		require_once "koneksi.php";
		$this->conmysql = $conmysql;
	}

	public function getApiGagal()
	{
		$query = "select * from API_Post_Gagal
			where jml_post < 3 and Keterangan != 'INVALID' and Keterangan !='POSTED' order by Running_Date asc limit 10";
		$result = mysqli_query($this->conmysql, $query);
		$arrPostGagal = array();

		while ($row = mysqli_fetch_array($result)) {
			// print("<pre>".print_r($row,true)."</pre>");
			array_push($arrPostGagal, $row);
		}
		return $arrPostGagal;
	}

	public function setDataBSPI($type, $JO_Number, $Index_Load, $RecordDate, $Load_Size, $materialstr)
	{
		$url = "http://192.168.100.17/apberp" . $type . "/erp/eaccounting/tools/api/sfservice.cfc?method=setDataBSPI&JO_Number=" . $JO_Number . "&Reference=" . $Index_Load . "&Trx_Date=" . $RecordDate . "&Ship_Distance=1&Output_Qty=" . $Load_Size . "&Input_Qty=" . $materialstr;

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
		// var_dump($result);
		// echo "<br>";
		return $result;
	}

	public function updateStatusPost($Index_Post_Gagal, $Keterangan)
	{
		//update status post
		$query = "UPDATE `API_Post_Gagal` SET `Keterangan`= '$Keterangan', jml_post = jml_post + 1, Running_Date = NOW() 
					where Index_Post_Gagal = " . $Index_Post_Gagal;
		if (mysqli_query($this->conmysql, $query)) {
			echo '<br>Update Status Post Berhasil<br><br>';
		} else {
			echo '<br>Update Status Post Gagal<br>';
		}
	}

	public function setDataBSP($type, $JO_Number, $SO_Number, $index_load, $RecordDate, $Load_Size, $material, $Driver_Code, $Truck_Code, $Ticket_Code)
	{
		$url = 	"http://192.168.100.17/apberp" . $type . "/erp/eaccounting/tools/api/sfservice.cfc?method=setDataBSP&JO_Number=" . $JO_Number . "&SO_Number=" . $SO_Number . "&Reference=" . $index_load . "&Trx_Date=" . $RecordDate . "&Ship_Distance=1&Output_Qty=" . $Load_Size . "&Input_Qty=" . $material . "&Driver=" . $Driver_Code . "&Vehicle_No=" . $Truck_Code . "&Trx_ID=" . $Ticket_Code;

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

	public function getDataBSPI($type)
	{
		$url = "http://192.168.100.17/apberp" . $type . "/erp/eaccounting/tools/api/sfservice.cfc?method=getDataBSPI";
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
		return $result['databsp'];
	}

	public function getDataBSP($type)
	{
		$url = "http://192.168.100.17/apberp" . $type . "/erp/eaccounting/tools/api/sfservice.cfc?method=getDataBSP2";
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

		return $result['databsp2'];
	}

	public function getLoadJoNumber($JO_Number, $Index_Load)
	{
		$query = "select max(a.index_load) as index_load ,a.LoadID, max(a.RecordDate) as RecordDate, a.Load_Size, a.Item_Code, a.BP_ID, a.CreatedBy, a.RecordDate, b.bp_name, c.region_name
						from `V_BatchSetupTickets` a
							left join `Batching_plant` b on a.BP_ID=b.id_bp
							left join `Region` c on b.id_region=c.id_region
						where a.Delivery_Instruction = '" . $JO_Number . "' and index_load = " . $Index_Load . " group by LoadID order by index_load asc limit 1";

		// echo $query."<br><br>";
		$result =  mysqli_query($this->conmysql, $query);

		$arrLoadJoNumber = array();
		while ($load = mysqli_fetch_array($result)) {
			$arrLoadJoNumber = $load;
		}
		return $arrLoadJoNumber;
	}

	public function logPost($type, $JO_Number, $Jo_Item_Code, $Load_Item_Code, $Item_Qty, $Remaining_Qty, $total_qty_load, $Load_Size, $bp_name, $Created_By,  $Created_Date, $Updated_By, $Last_Update, $CreatedBy, $RecordDate, $region_name, $index_load, $materialstr)
	{
		$query = "INSERT INTO `API_Logs_Detail`(`type`, `Method`, `JO_Number`, `Jobmix_ERP`, `Jobmix_BP`, `Req_Qty`, `Remain_Qty`, `Post_Qty`, `BP`, `CreateByERP`, `CreateDateERP`, `UpdateByERP`, `UpdateDateERP`, CreateByBP, CreateDateBP, Regional,
								`Material`, `Keterangan`, `Index_Load`) VALUES ('" . $type . "', 'POST', '" . $JO_Number . "', '" . $Jo_Item_Code . "', '" . $Load_Item_Code . "', " . $Item_Qty . ", " . ($Remaining_Qty - $total_qty_load) . ", " . $Load_Size . ", '" . $bp_name . "', '" . $Created_By . "', 
								" . $Created_Date . ", '" . $Updated_By . "', " . $Last_Update . ", '" . $CreatedBy . "', " . $RecordDate . ", '" . $region_name . "', '" . $materialstr . "', 'sukses - auto repost', " . $index_load . ")";

		if (mysqli_query($this->conmysql, $query)) {
			return true;
		} else {
			echo $query;
			return false;
		}
	}


	public function updateApiHeader($Remaining_Qty, $Load_Size, $JO_Number)
	{
		$query = "update `API_Logs_Header` SET `Remaining_Qty`=" . ($Remaining_Qty - $Load_Size) . " WHERE JO_Number='" . $JO_Number . "'";
		if (mysqli_query($this->conmysql, $query)) {
			return true;
		}
	}

	public function insertApiFinish($type, $Created_By, $Formula_Number, $Item_Qty, $JO_Number, $SIP_Number, $Created_Date, $Item_Code, $Item_Name, $JO_Reference, $Last_Update, $MR_Number, $Remaining_Qty, $Load_Size, $Updated_By, $Estimated_TimeProduction, $Material_Name, $JO_Date, $Material_Code)
	{

		$query = "insert INTO `API_Finish`(`type`, `Created_By`, `Formula_Number`, `Item_Qty`, `JO_Number`, `SIP_Number`, `Created_Date`, `Item_Code`, `Item_Name`, `JO_Reference`,
					`Last_Update`, `MR_Number`, `Remaining_Qty`, `Updated_By`, `Estimated_TimeProduction`, `Material_Name`, `JO_Date`, `Material_Code`) VALUES ('" . $type . "', '" . $Created_By . "', 
					'" . $Formula_Number . "', " . $Item_Qty . ", '" . $JO_Number . "', '" . $SIP_Number . "', " . $Created_Date . ", '" . $Item_Code . "', '" . $Item_Name . "', 
					'" . $JO_Reference . "', " . $Last_Update . ", '" . $MR_Number . "', " . ($Remaining_Qty - $Load_Size) . ", '" . $Updated_By . "', '" . $Estimated_TimeProduction . "', '" . $Material_Name . "', 
					'" . $JO_Date . "', '" . $Material_Code . "')";

		if (mysqli_query($this->conmysql, $query)) {
			return true;
		} else {
			echo $query;
			return false;
		}
	}
}


$api_repost = new Api_repost();

$arrPostGagal = $api_repost->getApiGagal();

foreach ($arrPostGagal as $post_gagal) {

	$RecordDate = date_format(date_create($post_gagal['RecordDate']), 'Y-m-d');
	$materialstr = $post_gagal['Material'];

	// generate string material akumulasi
	$arr_pasangan_material = explode('-', $materialstr);

	$material_akumulasi = [];
	foreach ($arr_pasangan_material as $pasangan_material) {
		$per_material = explode('|', $pasangan_material);
		if (isset($material_akumulasi[$per_material[0]]) && $material_akumulasi[$per_material[0]] != 'xxx') {
			$material_akumulasi[$per_material[0]] = $material_akumulasi[$per_material[0]] + $per_material[1];
		} else {
			$material_akumulasi[$per_material[0]] = $per_material[1];
		}
	}

	$arr_material_akumulasi_str = [];
	foreach ($material_akumulasi as $x => $y) {
		$arr_material_akumulasi_str[] = $x . '|' . $y;
	}
	$material_akumulasi = implode('-', $arr_material_akumulasi_str);


	if ($post_gagal['type'] == 'live-bspi') {
		$setDataBSP = $api_repost->setDataBSPI('', $post_gagal['JO_Number'], $post_gagal['Index_Load'], $RecordDate, $post_gagal['Load_Size'], $material_akumulasi);
	} else if ($post_gagal['type'] == 'dev-bspi') {
		$setDataBSP = $api_repost->setDataBSPI('dev', $post_gagal['JO_Number'], $post_gagal['Index_Load'], $RecordDate, $post_gagal['Load_Size'], $material_akumulasi);
	} else if ($post_gagal['type'] == 'dev-bsp') {
		$setDataBSP = $api_repost->setDataBSP('dev', $post_gagal['JO_Number'], $post_gagal['SO_Number'], $post_gagal['Index_Load'], $RecordDate, $post_gagal['Load_Size'], $material_akumulasi, $post_gagal['Driver_Code'], $post_gagal['Truck_Code'], $post_gagal['Ticket_Code']);
	}



	if ($setDataBSP['return'] == 1) {
		echo 'POST BERHASIL<br>' . $url;

		$updateStatusPost = $api_repost->updateStatusPost($post_gagal['Index_Post_Gagal'], 'POSTED');

		$dataBSPI_BSP = '';

		if ($post_gagal['type'] == 'live-bspi') {
			$dataBSPI_BSP = $api_repost->getDataBSPI('');
		} else if ($post_gagal['type'] == 'dev-bspi') {
			$dataBSPI_BSP = $api_repost->getDataBSPI('dev');
		} else if ($post_gagal['type'] == 'dev-bsp') {
			$dataBSPI_BSP	= $api_repost->getDataBSP('dev');
		}

		//INSERT LOG POST
		if (!empty($dataBSPI_BSP)) {
			//GET JO DATA
			$key = array_search($post_gagal['JO_Number'], array_column($dataBSPI_BSP, 'JO_Number'));
			$dataJO = $dataBSPI_BSP[$key];

			//SELECT LOAD JO NUMBER
			$arrLoadJoNumber = $api_repost->getLoadJoNumber($post_gagal['JO_Number'], $post_gagal['Index_Load']);
			// echo '<br>';
			// var_dump($arrLoadJoNumber);
			// echo '<br>';
			// var_dump($dataJO);
			// echo '<br>';
			// var_dump($post_gagal);
			// echo '<br>';
			echo '<br>';

			$dataJO['Created_Date'] = !empty($dataJO['Created_Date']) ? "'" . $dataJO['Created_Date'] . "'" : "NULL";
			$dataJO['Last_Update'] = !empty($dataJO['Last_Update']) ? "'" . $dataJO['Last_Update'] . "'" : "NULL";
			$arrLoadJoNumber['RecordDate'] = !empty($arrLoadJoNumber['RecordDate']) ? "'" . $arrLoadJoNumber['RecordDate'] . "'" : "NULL";


			//INSERT LOG API DETAIL
			$logPost = $api_repost->logPost($post_gagal['type'], $dataJO['JO_Number'], $dataJO['Item_Code'], $arrLoadJoNumber['Item_Code'], $dataJO['Item_Qty'], $dataJO['Remaining_Qty'], $post_gagal['Load_Size'], $arrLoadJoNumber['Load_Size'], $arrLoadJoNumber['bp_name'], $dataJO['Created_By'],  $dataJO['Created_Date'], $dataJO['Updated_By'], $dataJO['Last_Update'], $arrLoadJoNumber['CreatedBy'], $arrLoadJoNumber['RecordDate'], $arrLoadJoNumber['region_name'], $arrLoadJoNumber['index_load'], $materialstr);
			if ($logPost) {
				echo '<br>Insert Log API Detail Berhasil';
			} else {
				echo '<br>Insert Log API Detail Gagal<br>';
			}

			//UPDATE API HEADER
			$updateApiHeader = $api_repost->updateApiHeader($dataJO['Remaining_Qty'], $post_gagal['Load_Size'], $post_gagal['JO_Number']);
			if ($updateApiHeader) {
				echo '<br>UPDATE Log API Header Berhasil';
			} else {
				echo '<br>UPDATE Log API Header Gagal<br>';
			}

			if ($dataJO['Remaining_Qty'] - $post_gagal['Load_Size'] <= 0) {

				$dataJO['Created_Date'] = !empty($dataJO['Created_Date']) ? "'" . $dataJO['Created_Date'] . "'" : "NULL";
				$dataJO['Last_Update'] = !empty($dataJO['Last_Update']) ? "'" . $dataJO['Last_Update'] . "'" : "NULL";

				// INSERT JO FINISH
				$insertApiFinish = $api_repost->insertApiFinish($post_gagal['type'], $dataJO['Created_By'], $dataJO['Formula_Number'], $dataJO['Item_Qty'], $dataJO['JO_Number'], $dataJO['SIP_Number'], $dataJO['Created_Date'], $dataJO['Item_Code'], $dataJO['Item_Name'], $dataJO['JO_Reference'], $dataJO['Last_Update'], $dataJO['MR_Number'], $dataJO['Remaining_Qty'], $post_gagal['Load_Size'], $dataJO['Updated_By'], $dataJO['Estimated_TimeProduction'], $dataJO['Material_Name'], $dataJO['JO_Date'], $dataJO['Material_Code']);

				if ($insertApiFinish) {
					echo '<br>INSERT API Finish Berhasil';
				} else {
					echo '<br>INSERT API Finish Gagal<br>';
				}
			}
		}

		echo '<div class="col-12"><div class="alert alert-success">' . $alert . '</div></div>';
	} else {
		$api_repost->updateStatusPost($post_gagal['Index_Post_Gagal'], $setDataBSP['return']);
		echo '<div class="col-12"><div class="alert alert-danger">POST GAGAL<br>' . $setDataBSP['return'] . '</div></div><br>';
	}
	echo '<br>==========================================================================================================<br>';
	sleep(1);
}
