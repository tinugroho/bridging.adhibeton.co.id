<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_produksi extends CI_Model
{
	///////////////////////////////////////////////////////////////////////////////////////////
	// 									LOAD												 //
	///////////////////////////////////////////////////////////////////////////////////////////
	public function viewRegion($id_region, $start = '', $end = '', $length = '', $offset = '', $search = '', $order = '', $BP_ID = '', $ada_jo, $ada_sklp, $mesin = 'commandbatch')
	{
		// echo $ada_jo . $ada_sklp . '<br>';
		if ($mesin == 'commandbatch') {
			return $this->commandbatch($id_region, $start, $end, $length, $offset, $search, $order, $BP_ID, $ada_jo, $ada_sklp);
		} else if ($mesin == 'autobatch') {
			return $this->autobatch($id_region, $start, $end, $length, $offset, $search, $order, $BP_ID);
		} else if ($mesin == 'eurotech') {
			return $this->eurotech($id_region, $start, $end, $length, $offset, $search, $order, $BP_ID);
		} else {
			return array();
		}
	}

	public function commandbatch($id_region, $start = '', $end = '', $length = '', $offset = '', $search = '', $order = '', $BP_ID = '', $ada_jo, $ada_sklp)
	{
		// record date
		if ($start == '' && $end == '') {
			$record_date = " and a.RecordDate BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() + INTERVAL 1 DAY ";
		} else {
			if ($start == '') {
				$start = "2018-01-01 00:00";
			}
			if ($end == '') {
				$end = "CURDATE() + INTERVAL 1 DAY";
			} else {
				$end = "'$end'";
			}

			$record_date = " and a.RecordDate BETWEEN '" . $start . "' AND " . $end . " ";
		}
		// record date

		$length = $length != '' ? ' limit ' . $length : '';
		$offset = $offset != '' ? ' offset ' . $offset : '';

		$having = is_numeric($search) ? " having (index_load like '%$search%' or max_ticket like '%$search%' or ROUND(Ordered_Qty, 2) like '%$search%' or total_load like '%$search%' or total_batch like '%$search%' 
										or ROUND(Delivered_Qty,2) like '%$search%' ) " : '';
		$search = ($search != '' && !is_numeric($search)) ? " and CONCAT_WS(' ', a.Delivery_Instruction, a.Item_Code, a.Customer_Description, b.bp_name) like '%$search%' " : '';

		$BP_ID = $BP_ID != '' ? " and a.BP_ID=$BP_ID " : '';

		// var_dump($ada_jo);
		$ada_jo = $ada_jo ? ' and (a.Delivery_Instruction is not null) ' : '';
		$ada_sklp = $ada_sklp ? ' and ((a.Job_Code is not null) or (a.PO_Num is not null)) ' : '';

		// order $order['column']-$order['dir']
		$order 	= $order  != '' ? ' order by ' . $order['column'] . ' ' . $order['dir'] . ' ' : " order by tanggal desc ";

		// $group_by = " group by 
		// 			CASE WHEN OrderID IS NOT NULL 
		// 			THEN a.OrderID
		// 			ELSE a.Delivery_Instruction
		// 			END
		// 			, a.Item_Code, a.BP_ID ";
		$group_by = "";

		$query = "	select a.index_load, a.OrderID, a.Ticket_code max_ticket, a.Load_Size, a.Other_Code, a.Consistence,
						
						case 
							when (a.PO_Num is not null) then a.PO_Num 
							when (a.Job_Code is not null) then a.Job_Code 
							else ''
						end sklp, 

						case when (a.OrderID is not null and a.OrderID !='') 
							then a.Ordered_Qty 
							else a.Price_Qty 
						end Ordered_Qty, 
						
						a.LoadID, a.Delivery_Instruction, a.Item_Code, a.Customer_Description, a.RecordDate as tanggal,1 as total_load, a.Max_Batch as total_batch, 
						
						case when (a.OrderID is not null and a.OrderID !='') 
							then a.Delivered_Qty
							else a.Load_Size
						end Delivered_Qty, 

						a.Ticket_Status, a.BP_ID, b.bp_name, b.api type, c.region_name,

						ld.Index_Log as api_sukses, lg.Index_Post_Gagal as api_gagal,
						
						(
							select Index_Load from API_Logs_Detail where Method = 'POST' and JO_Number = a.Delivery_Instruction and `type`=b.api 
							union
							(select Index_Load from API_Post_Gagal where JO_Number = a.Delivery_Instruction and `type`=b.api)
							order by Index_Load desc limit 1
						) api_sukses_last_id
					from `V_BatchSetupTickets` a
					left join `Batching_plant` b on b.id_bp = a.BP_ID
					left join `Region` c on c.id_region = b.id_region
					left join `API_Logs_Detail` ld on a.index_load=ld.Index_Load and ld.Method = 'POST'
					left join `API_Post_Gagal` lg on a.index_load=lg.Index_Load
					where 1 and (a.Ticket_Status ='C' or a.Ticket_Status ='O') and c.id_region = $id_region $record_date $search $BP_ID $ada_jo $ada_sklp 
					$group_by 
					$having 
					$order $length $offset ";
		// echo $query;
		return $this->db->query($query)->result();
	}

	public function autobatch($id_region, $start = '', $end = '', $length = '', $offset = '', $search = '', $order = '', $BP_ID = '')
	{
		$autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.

		// record date
		if ($start == '' && $end == '') {
			$record_date = " and y.tanggal BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() + INTERVAL 1 DAY ";
		} else {
			if ($start == '') {
				$start = "2018-01-01 00:00";
			}
			if ($end == '') {
				$end = "CURDATE() + INTERVAL 1 DAY";
			} else {
				$end = "'$end'";
			}

			$record_date = " and y.tanggal BETWEEN '" . $start . "' AND " . $end . " ";
		}
		// record date

		$length = $length != '' ? ' limit ' . $length : '';
		$offset = $offset != '' ? ' offset ' . $offset : '';

		$having = is_numeric($search) ? " having (max_ticket like '%$search%' or ROUND(Ordered_Qty, 2) like '%$search%' or total_load like '%$search%' or total_batch like '%$search%' 
										or ROUND(Delivered_Qty,2) like '%$search%' ) " : '';
		$search = ($search != '' && !is_numeric($search)) ? " and CONCAT_WS(' ', Delivery_Instruction, Item_Code, Customer_Description, bp_name) like '%$search%' " : '';

		$BP_ID = $BP_ID != '' ? " and a.BP_ID=$BP_ID " : '';
		$order 	= $order  != '' ? ' order by ' . $order['column'] . ' ' . $order['dir'] . ' ' : " order by tanggal desc ";

		$query = 	"select x.total_batch total_batch, x.Delivered_Qty Load_Size, y.tanggal tanggal, y.PO_Number sklp,
					y.max_ticket max_ticket, y.Delivery_Instruction, y.Jobmix_Id, y.Item_Code, y.Ordered_Qty Ordered_Qty, y.BP_ID, y.total_load total_load, y.BP_Name bp_name,
					y.Customer_Description, y.OrderID, y.Ticket_Status, y.index_load,
					y.api_sukses, y.api_gagal, y.api_sukses_last_id, y.type
					from (
						select a.index_load, a.Ticket_Id max_ticket, PO_Number, a.Jobmix_Id, b.Jobmix_Code Item_Code, a.Qty_Jobmix Ordered_Qty, a.BP_ID, 1 total_load, a.BP_Name bp_name, a.Createdate tanggal,
							bp.api type,
							case
								when bp.JO_Column='Jo_Number' then a.Jo_Number
								when bp.JO_Column='PO_Number' then a.PO_Number
								when bp.JO_Column='Remarks' then a.Remarks
							end Delivery_Instruction,

							'Adhi Beton' Customer_Description, '' OrderID, 'O' Ticket_Status,

							ld.Index_Log as api_sukses, lg.Index_Post_Gagal as api_gagal,
							(
								select index_load from API_Logs_Detail where Method = 'POST' and 
									JO_Number = case
													when bp.JO_Column='Jo_Number' then a.Jo_Number
													when bp.JO_Column='PO_Number' then a.PO_Number
													when bp.JO_Column='Remarks' then a.Remarks
												end 
									and `type`=bp.api 
								union
								(select index_load from API_Post_Gagal where 
									JO_Number = case
													when bp.JO_Column='Jo_Number' then a.Jo_Number
													when bp.JO_Column='PO_Number' then a.PO_Number
													when bp.JO_Column='Remarks' then a.Remarks
												end 
									and `type`=bp.api)
								order by index_load desc limit 1
							) api_sukses_last_id

						from TICKET a inner join JOBMIX_HEADER b on a.Jobmix_Id = b.Jobmix_Id 
						inner join Batching_plant bp on a.BP_ID = bp.id_bp 
						left join `API_Logs_Detail` ld on a.index_load=ld.index_load and ld.Method = 'POST'
						left join `API_Post_Gagal` lg on a.index_load=lg.index_load
						where bp.id_region = $id_region $BP_ID
						) y
					inner join (
						select a.Ticket_Id, count(a.Ticket_Id) total_batch, sum(a.Qty_Jobmix) Delivered_Qty
						FROM BATCH_HEADER a 
						inner join Batching_plant bp on a.BP_ID = bp.id_bp 
						where bp.id_region = $id_region $BP_ID
						group by Ticket_Id
					) x on x.Ticket_Id = y.max_ticket
					where 1 $record_date $search
					$having				
					$order $length $offset ";
		// echo $query;
		return $autobatch->query($query)->result();
	}

	public function eurotech($id_region, $start = '', $end = '', $length = '', $offset = '', $search = '', $order = '', $BP_ID = '')
	{
		$eurotech = $this->load->database('eurotech', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.

		// record date
		if ($start == '' && $end == '') {
			$record_date = " and Sheet.date_mix BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() + INTERVAL 1 DAY ";
		} else {
			if ($start == '') {
				$start = "2018-01-01 00:00";
			}
			if ($end == '') {
				$end = "CURDATE() + INTERVAL 1 DAY";
			} else {
				$end = "'$end'";
			}

			$record_date = " and Sheet.date_mix BETWEEN '" . $start . "' AND " . $end . " ";
		}
		// record date

		$length = $length != '' ? ' limit ' . $length : '';
		$offset = $offset != '' ? ' offset ' . $offset : '';

		$having = is_numeric($search) ? " having (max_ticket like '%$search%' or ROUND(Ordered_Qty, 2) like '%$search%' or total_load like '%$search%' or total_batch like '%$search%' 
										or ROUND(Delivered_Qty,2) like '%$search%' ) " : '';
		$search = ($search != '' && !is_numeric($search)) ? " and CONCAT_WS(' ', order_description01, receipe_code, customer, bp_name) like '%$search%' " : '';

		$BP_ID = $BP_ID != '' ? " and Sheet.bp_id=$BP_ID " : '';
		$order 	= $order  != '' ? ' order by ' . $order['column'] . ' ' . $order['dir'] . ' ' : " order by tanggal desc ";

		$query = "	select `index`, sheet_no, delivery_no_order as max_ticket, order_description01 as Delivery_Instruction, order_description02 as sklp, receipe_code as Item_Code, 
						m3_delivered Ordered_Qty, m3_delivery as Delivered_Qty, m3_delivery as Load_Size, customer as Customer_Description, date_end as tanggal, 1 as total_load, 
						batch_total as total_batch, bp_id as BP_ID, Batching_plant.bp_name as bp_name
					FROM `Sheet` 
					inner join Batching_plant on Batching_plant.id_bp = Sheet.bp_id 
					where Batching_plant.id_region = $id_region $BP_ID $record_date $search
					$having		
					$order $length $offset ";
		// echo $query;
		return $eurotech->query($query)->result();
	}


	///////////////////////////////////////////////////////////////////////////////////////////
	// 								DETAIL LOAD												 //
	///////////////////////////////////////////////////////////////////////////////////////////
	public function detailLoadProduksi($id_region, $LoadID = '', $Ticket_Id = '', $sheet_no = '', $start = '', $end = '', $BP_ID, $mesin = 'commandbatch')
	{
		if ($mesin == 'commandbatch') {
			return $this->detailLoadCommandBatch($id_region, $LoadID, $start, $end, $BP_ID);
		} else if ($mesin == 'autobatch') {
			return $this->detailLoadAutobatch($id_region, $Ticket_Id, $start, $end, $BP_ID);
		} else if ($mesin == 'eurotech') {
			return $this->detailLoadEurotech($id_region, $sheet_no, $start, $end, $BP_ID);
		} else {
			return array();
		}
	}

	public function detailLoadCommandBatch($id_region, $LoadID = '', $start = '', $end = '', $BP_ID)
	{
		$BP_ID_ori = $BP_ID;
		$LoadID_ori = $LoadID;


		$BP_ID 					= $BP_ID != "" ? " and a.BP_ID='" . $BP_ID . "' " : "";
		$id_region 				= $id_region != "" ? " and c.id_region=" . $id_region . " " : "";
		$LoadID 				= "and a.LoadID='" . $LoadID . "' ";

		// record date
		if ($start == '' && $end == '') {
			$record_date = " and a.RecordDate BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() + INTERVAL 1 DAY ";
		} else {
			if ($start == '') {
				$start = "2018-01-01 00:00";
			}
			if ($end == '') {
				$end = "CURDATE() + INTERVAL 1 DAY";
			} else {
				$end = "'$end'";
			}

			$record_date = " and a.RecordDate BETWEEN '" . $start . "' AND " . $end . " ";
		}
		// record date

		$query = "	select a.index_load, a.OrderID, a.Ticket_Code, a.Order_Number_Of_Tickets, a.LoadID, a.Ordered_Qty, a.Price_Qty, a.Delivered_Qty, a.Load_Size, a.Max_Batch, a.Delivery_Instruction, 
						a.CreatedBy, a.Driver_Name, a.Truck_Code, a.RecordDate, a.Item_Code, a.Customer_Description, a.Ticket_Status, a.BP_ID, a.PO_Num, a.Job_Code, b.bp_name, z.item,
						ld.Index_Log as api_sukses, lg.Index_Post_Gagal as api_gagal, lg.type as api_gagal_type, 
						(
							select Index_Load from API_Logs_Detail where Method = 'POST' and JO_Number = a.Delivery_Instruction and `type`=b.api 
							union
							(select Index_Load from API_Post_Gagal where JO_Number = a.Delivery_Instruction and `type`=b.api)
							order by Index_Load desc limit 1
						) api_sukses_last_id,
						b.api api_type
					from V_BatchSetupTickets a		
					inner join `Batching_plant` b on b.id_bp = a.BP_ID
					inner join `Region` c on c.id_region = b.id_region
					inner join (
						select LoadID, GROUP_CONCAT(item SEPARATOR '~') item 
						from ( 
							select `LoadID`, concat_ws('|',Item_Code, Net_Target_Amt, ROUND(sum(Net_Auto_Batched_Amt), 2), Amt_UOM) item
							from `Load_Lines` 
							where BP_ID = $BP_ID_ori and LoadID = '$LoadID_ori'
							group by Item_Code 
						) w 
						GROUP BY LoadID
					) z on a.LoadID=z.LoadID 
					left join `API_Logs_Detail` ld on a.index_load=ld.Index_Load and ld.Method = 'POST'
					left join `API_Post_Gagal` lg on a.index_load=lg.Index_Load
					where (a.Ticket_Status ='C' or a.Ticket_Status ='O') $id_region $LoadID $BP_ID $record_date 
					order by Ticket_Code desc";
		// echo $query;

		return $this->db->query($query)->result();
	}

	public function detailLoadAutobatch($id_region, $Ticket_Id = '', $start = '', $end = '', $BP_ID)
	{
		$autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.

		// record date
		if ($start == '' && $end == '') {
			$record_date = " and y.RecordDate BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() + INTERVAL 1 DAY ";
		} else {
			if ($start == '') {
				$start = "2018-01-01 00:00";
			}
			if ($end == '') {
				$end = "CURDATE() + INTERVAL 1 DAY";
			} else {
				$end = "'$end'";
			}

			$record_date = " and y.RecordDate BETWEEN '" . $start . "' AND " . $end . " ";
		}
		// record date

		$BP_ID = $BP_ID != '' ? " and a.BP_ID=$BP_ID " : '';
		$query = 	"select x.*,x.Driver Driver_Name, x.Truck Truck_Code, y.*, z.item, '' Order_Number_Of_Tickets
					from (
						select a.Ticket_Id max_ticket, 
							a.index_load,
							case
								when bp.JO_Column='Jo_Number' then a.Jo_Number
								when bp.JO_Column='PO_Number' then a.PO_Number
								when bp.JO_Column='Remarks' then a.Remarks
							end Delivery_Instruction,
							
							PO_Number sklp,
							a.Jobmix_Id, b.Jobmix_Code Item_Code, 
							a.Qty_Jobmix Load_Size, a.BP_ID, a.Createby CreatedBy, a.BP_Name bp_name, a.Createdate RecordDate,
							'Adhi Beton' Customer_Description, '' OrderID, 'O' Ticket_Status, '' Driver_Name, '' Truck_Code,
							ld.Index_Log as api_sukses, lg.Index_Post_Gagal as api_gagal, lg.type as api_gagal_type,
							(
								select index_load from API_Logs_Detail where Method = 'POST' and 
									JO_Number = case
													when bp.JO_Column='Jo_Number' then a.Jo_Number
													when bp.JO_Column='PO_Number' then a.PO_Number
													when bp.JO_Column='Remarks' then a.Remarks
												end 
									and `type`=bp.api 
								union
								(select index_load from API_Post_Gagal where 
									JO_Number = case
													when bp.JO_Column='Jo_Number' then a.Jo_Number
													when bp.JO_Column='PO_Number' then a.PO_Number
													when bp.JO_Column='Remarks' then a.Remarks
												end 
									and `type`=bp.api)
								order by index_load desc limit 1
							) api_sukses_last_id,						
							bp.api api_type
							a.
						from TICKET a inner join JOBMIX_HEADER b on a.Jobmix_Id = b.Jobmix_Id 
						inner join Batching_plant bp on a.BP_ID = bp.id_bp 
						left join `API_Logs_Detail` ld on a.index_load=ld.index_load and ld.Method = 'POST'
						left join `API_Post_Gagal` lg on a.index_load=lg.index_load
						where bp.id_region = $id_region $BP_ID
						) y
					inner join (
						select a.Ticket_Id Ticket_Code, sum(a.Qty_Jobmix) Price_Qty 
						FROM BATCH_HEADER a 
						inner join Batching_plant bp on a.BP_ID = bp.id_bp 
						where bp.id_region = $id_region $BP_ID
						group by Ticket_Id
					) x on x.Ticket_Code = y.max_ticket
					inner join (
						select Ticket_Id, GROUP_CONCAT(item SEPARATOR '~') item 
							from ( 
								SELECT a.Ticket_Id, concat_ws('|', a.Material_Code, ROUND(sum(a.Target_Qty), 2), ROUND(sum(a.Actual_Qty), 2), a.Material_Uom) item 
								FROM BATCH_DETAIL a
								where 1 $BP_ID
								GROUP BY Ticket_Id,Material_Code 
							) w GROUP BY Ticket_Id
							order by item asc
					) z on y.max_ticket=z.Ticket_Id 
					where 1 and Ticket_Id=$Ticket_Id $record_date 
					order by Ticket_Code desc";
		// echo $query;
		return $autobatch->query($query)->result();
	}

	public function detailLoadEurotech($id_region, $sheet_no = '', $start = '', $end = '', $BP_ID)
	{
		$eurotech = $this->load->database('eurotech', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.

		// record date
		if ($start == '' && $end == '') {
			$record_date = " and y.RecordDate BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() + INTERVAL 1 DAY ";
		} else {
			if ($start == '') {
				$start = "2018-01-01 00:00";
			}
			if ($end == '') {
				$end = "CURDATE() + INTERVAL 1 DAY";
			} else {
				$end = "'$end'";
			}

			$record_date = " and y.RecordDate BETWEEN '" . $start . "' AND " . $end . " ";
		}
		// record date

		$BP_ID = $BP_ID != '' ? " and bp_id=$BP_ID " : '';
		// $query = 	"select x.*, y.*, z.item, '' Order_Number_Of_Tickets
		// 			from (
		// 				select a.Ticket_Id max_ticket, a.Remarks Delivery_Instruction, a.Jobmix_Id, b.Jobmix_Code Item_Code, 
		// 				a.Qty_Jobmix Load_Size, a.BP_ID, a.Createby CreatedBy, a.BP_Name bp_name, a.Createdate RecordDate,
		// 				'Adhi Beton' Customer_Description, '' OrderID, 'O' Ticket_Status, '' Driver_Name, '' Truck_Code,
		// 				ld.Index_Log as api_sukses, lg.Index_Post_Gagal as api_gagal, lg.type as api_gagal_type
		// 				from TICKET a inner join JOBMIX_HEADER b on a.Jobmix_Id = b.Jobmix_Id 
		// 				inner join Batching_plant bp on a.BP_ID = bp.id_bp 
		// 				left join `API_Logs_Detail` ld on a.Ticket_Id=ld.Index_Load
		// 				left join `API_Post_Gagal` lg on a.Ticket_Id=lg.Index_Load
		// 				where bp.id_region = $id_region $BP_ID
		// 				) y
		// 			inner join (
		// 				select a.Ticket_Id Ticket_Code, sum(a.Qty_Jobmix) Price_Qty 
		// 				FROM BATCH_HEADER a 
		// 				inner join Batching_plant bp on a.BP_ID = bp.id_bp 
		// 				where bp.id_region = $id_region $BP_ID
		// 				group by Ticket_Id
		// 			) x on x.Ticket_Code = y.max_ticket
		// 			inner join (
		// 				select Ticket_Id, GROUP_CONCAT(item SEPARATOR '~') item 
		// 					from ( 
		// 						SELECT a.Ticket_Id, concat_ws('|', a.Material_Code, ROUND(sum(a.Target_Qty), 2), ROUND(sum(a.Actual_Qty), 2), a.Material_Uom) item 
		// 						FROM BATCH_DETAIL a
		// 						where 1 $BP_ID
		// 						GROUP BY Ticket_Id,Material_Code 
		// 					) w GROUP BY Ticket_Id
		// 					order by item asc
		// 			) z on y.max_ticket=z.Ticket_Id 
		// 			where 1 and Ticket_Id=$sheet_no $record_date 
		// 			order by Ticket_Code desc";
		$query = "	select 	
						a.index, a.sheet_no, a.order_no, a.order_description01, a.order_description02, a.customer Customer_Description, a.address_customer, a.site_code, a.site, a.receipe_code Item_Code, a.description01, a.type, a.slump, a.driver Driver_Name, a.truck_code Truck_Code, a.m3_delivered Delivered_Qty, a.m3_delivery Price_Qty, a.m3_delivery Load_Size, a.batch_total, a.date_end RecordDate, a.date_mix, a.delivery_no_order Ticket_Code, a.operator CreatedBy, a.bp_id,
						'' Order_Number_Of_Tickets, a.order_description01 Delivery_Instruction, ''OrderID,
						b.bp_name,
						
						(select GROUP_CONCAT(item SEPARATOR '~') item 
							from
							(select concat_ws('|', c.name_material, sum(c.sp_target), sum(c.pv_actually), c.unit_material) item
								from (select * from Sheet where sheet_no=$sheet_no $BP_ID order by date_end desc limit 1) a 
									inner join Data_Mixed b on b.sheet_no = a.sheet_no
									inner join Material_Arch c on c.batch_id = b.batch_no
								where a.sheet_no = $sheet_no
								group by a.delivery_no_order, c.name_material
							) item
						) item,						
						b.api api_type
					from (select * from Sheet where sheet_no=$sheet_no $BP_ID order by date_end desc limit 1) a
					inner join Batching_plant b on a.bp_id=b.id_bp ";
		// echo $query;
		return $eurotech->query($query)->result();
	}

	public function detailLoadMaterial($LoadID_TicketID, $BP_ID, $mesin = 'commandbatch')
	{
		if ($mesin == 'commandbatch') {
			return $this->detailLoadMaterialCommandBatch($LoadID_TicketID, $BP_ID);
		} else if ($mesin == 'autobatch') {
			return $this->detailLoadMaterialAutoBatch($LoadID_TicketID, $BP_ID);
		} else {
			return array();
		}
	}

	public function detailLoadMaterialCommandBatch($LoadID, $BP_ID)
	{
		$query = "select Item_Code, Net_Target_Amt as Target, sum(Net_Auto_Batched_Amt) as Auto, Amt_UOM from `Load_Lines` 
                        where 1 and LoadID='" . $LoadID . "' and BP_ID=" . $BP_ID . " 
                        group by Item_Code 
                        order by Sort_Line_Num ASC";

		return $this->db->query($query)->result();
	}
	public function detailLoadMaterialAutoBatch($Ticket_id, $BP_ID)
	{
		$autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
		$query = "select Material_Code Item_Code, sum(Target_Qty) as Target, sum(Actual_Qty) as Auto, Material_Uom Amt_UOM from BATCH_DETAIL 
                        where 1 and Ticket_id=" . $Ticket_id . " and BP_ID=" . $BP_ID . " 
                        group by Material_Code 
                        order by Material_Code ASC";

		return $autobatch->query($query)->result();
	}


	///////////////////////////////////////////////////////////////////////////////////////////
	// 									DETAIL BATCH										 //
	///////////////////////////////////////////////////////////////////////////////////////////
	public function detailbathProduksi($id_region, $LoadID = '', $Delivery_Instruction = '', $Item_Code = '', $start = '', $end = '', $BP_ID = '', $mesin = 'commandbatch')
	{
		if ($mesin == 'commandbatch') {
			return $this->detailBathCommandBatch($id_region, $LoadID, $Delivery_Instruction, $Item_Code, $start, $end, $BP_ID);
		} else if ($mesin == 'autobatch') {
			return $this->detailBatchAutobatch($id_region, $LoadID, $Delivery_Instruction, $Item_Code, $start, $end, $BP_ID);
		} else {
			return array();
		}
	}
	public function detailBathCommandBatch($id_region, $LoadID = '', $Delivery_Instruction = '', $Item_Code = '', $start = '', $end = '', $BP_ID = '')
	{
		$BP_ID 					= $BP_ID != "" ? " and a.BP_ID='" . $BP_ID . "'  and b.BP_ID='" . $BP_ID . "'  and bl.BP_ID='" . $BP_ID . "' " : "";
		$id_region 				= $id_region != "" ? " and c.id_region=" . $id_region . " " : "";
		$LoadID 				= " and a.LoadID='" . $LoadID . "' ";


		// record date
		if ($start == '' && $end == '') {
			$record_date = " and a.RecordDate BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() + INTERVAL 1 DAY ";
		} else {
			if ($start == '') {
				$start = "2018-01-01 00:00";
			}
			if ($end == '') {
				$end = "CURDATE() + INTERVAL 1 DAY";
			} else {
				$end = "'$end'";
			}

			$record_date = " and a.RecordDate BETWEEN '" . $start . "' AND " . $end . " ";
		}
		// record date

		$query = "select
			index_load ,Delivery_Instruction, Ticket_Code, CreatedBy, Jobmix, Customer_Description, 
			Ticket_Status, Batch_Num, Batch_Size, RecordDate,
			GROUP_CONCAT(item separator '-') item, BatchID, bp_name, api_sukses, api_gagal, api_gagal_type
		from(
			SELECT 
				a.index_load, a.Delivery_Instruction, a.Ticket_Code, a.CreatedBy, a.Item_Code Jobmix, a.Customer_Description, 
				a.Ticket_Status, b.Batch_Num, b.Batch_Size, max(b.RecordDate) RecordDate, 
				concat_ws('|',bl.Item_Code, bl.Net_Target_Amt ,bl.Net_Batched_Amt, bl.Amt_UOM) item,
				bl.BatchID, bp.bp_name, ld.Index_Log as api_sukses, lg.Index_Post_Gagal as api_gagal, lg.type as api_gagal_type
			FROM V_BatchSetupTickets a 
			inner JOIN `Batch` b ON b.LoadID = a.LoadID 
			inner JOIN `Batch_Line` bl ON bl.BatchID = b.BatchID 
			inner join `Batching_plant` bp on bp.id_bp = a.BP_ID 
			left join `API_Logs_Detail` ld on a.index_load=ld.Index_Load and ld.Method = 'POST'
			left join `API_Post_Gagal` lg on a.index_load=lg.Index_Load
			WHERE 1 and (a.Ticket_Status ='C' or a.Ticket_Status ='O') $LoadID $record_date $BP_ID
			group by bl.BatchID, b.Batch_Num, a.Ticket_Status ,bl.Item_Code
			ORDER BY a.Ticket_Code desc, b.Batch_Num desc, a.Ticket_Status asc 
		) x
		group by BatchID, Batch_Num, Ticket_Status 
		ORDER BY Ticket_Code desc, Batch_Num desc, Ticket_Status asc ";
		// echo $query;
		return $this->db->query($query)->result();
	}

	public function detailBatchAutobatch($id_region, $Ticket_Id = '', $Delivery_Instruction = '', $Item_Code = '', $start = '', $end = '', $BP_ID)
	{
		$autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.

		// record date
		if ($start == '' && $end == '') {
			$record_date = " and y.Createdate BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() + INTERVAL 1 DAY ";
		} else {
			if ($start == '') {
				$start = "2018-01-01 00:00";
			}
			if ($end == '') {
				$end = "CURDATE() + INTERVAL 1 DAY";
			} else {
				$end = "'$end'";
			}

			$record_date = " and y.Createdate BETWEEN '" . $start . "' AND " . $end . " ";
		}
		// record date

		$BP_ID = $BP_ID != '' ? " and a.BP_ID=$BP_ID " : '';
		$Item_Code 				= $Item_Code != "" ? " and y.Jobmix='" . $Item_Code . "' " : "";
		$Delivery_Instruction 	= $Delivery_Instruction != "" ? " and y.Delivery_Instruction='" . $Delivery_Instruction . "' " : " and (y.Delivery_Instruction IS NULL OR y.Delivery_Instruction = '') ";

		$query = 	"select x.*, y.*, z.item, '' Order_Number_Of_Tickets
					from (
						select a.Ticket_Id max_ticket, 
						
						case
							when bp.JO_Column='Jo_Number' then a.Jo_Number
							when bp.JO_Column='PO_Number' then a.PO_Number
							when bp.JO_Column='Remarks' then a.Remarks
						end Delivery_Instruction,
						
						a.Jobmix_Id, b.Jobmix_Code Jobmix, 
						a.Qty_Jobmix Load_Size, a.BP_ID, a.Createby CreatedBy, a.BP_Name bp_name, a.Createdate,
						'Adhi Beton' Customer_Description, '' OrderID, 'O' Ticket_Status, '' Driver_Name, '' Truck_Code,
						ld.Index_Log as api_sukses, lg.Index_Post_Gagal as api_gagal, lg.type as api_gagal_type
						from TICKET a inner join JOBMIX_HEADER b on a.Jobmix_Id = b.Jobmix_Id 
						inner join Batching_plant bp on a.BP_ID = bp.id_bp 
						left join `API_Logs_Detail` ld on a.Ticket_Id=ld.Index_Load and ld.Method = 'POST'
						left join `API_Post_Gagal` lg on a.Ticket_Id=lg.Index_Load
						where bp.id_region = $id_region $BP_ID
						) y
					inner join (
						select a.Ticket_Id Ticket_Code, sum(a.Qty_Jobmix) Batch_Size , Batch_Id Batch_Num, Endbatch RecordDate
						FROM BATCH_HEADER a 
						inner join Batching_plant bp on a.BP_ID = bp.id_bp 
						where bp.id_region = $id_region $BP_ID
						group by Ticket_Id, Batch_Id
					) x on x.Ticket_Code = y.max_ticket
					inner join (
						select Ticket_Id, Batch_Id, GROUP_CONCAT(item SEPARATOR '-') item 
							from ( 
								SELECT a.Ticket_Id, a.Batch_Id, concat_ws('|', a.Material_Code, ROUND(sum(a.Target_Qty), 2), ROUND(sum(a.Actual_Qty), 2), a.Material_Uom) item 
								FROM BATCH_DETAIL a
								where 1 $BP_ID
								GROUP BY Ticket_Id, Batch_Id, Material_Code 
							) w GROUP BY Ticket_Id, Batch_Id
							order by item asc
					) z on x.Ticket_Code=z.Ticket_Id and x.Batch_Num=z.Batch_Id
					where 1 and Ticket_Code=$Ticket_Id $record_date 
					order by Ticket_Code desc, Batch_Num desc";
		// echo $query;
		return $autobatch->query($query)->result();
	}


	///////////////////////////////////////////////////////////////////////////////////////////
	// 									Update JO Number									 //
	///////////////////////////////////////////////////////////////////////////////////////////
	public function updateJoNumber($LoadID, $Ticket_Id, $sheet_no, $JO_Number, $change_all, $mesin)
	{
		if ($mesin == 'commandbatch') {
			return $this->updateJoNumber_commandbatch($LoadID, $JO_Number, $change_all);
		} else if ($mesin == 'autobatch') {
			return $this->updateJoNumber_autobatch($Ticket_Id, $JO_Number, $change_all);
		} else if ($mesin == 'eurotech') {
			return $this->updateJoNumber_eurotech($sheet_no, $JO_Number, $change_all);
		}
	}

	public function updateJoNumber_commandbatch($LoadID, $JO_Number, $change_all)
	{
		if (!empty($change_all)) {
			$whereSql = "Delivery_Instruction='" . $change_all . "'";
		} else {
			$whereSql = "LoadID='" . $LoadID . "'";
		}
		return $this->db->query("update V_BatchSetupTickets set Delivery_Instruction='" . $JO_Number . "' where $whereSql");
	}
	public function updateJoNumber_autobatch($Ticket_Id, $JO_Number, $change_all)
	{
		$autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
		$column_name = $autobatch->query("select b.JO_Column from TICKET a, Batching_plant b where a.BP_ID=b.id_bp and a.Ticket_Id=$Ticket_Id")->row()->JO_Column;
		if (!empty($change_all)) {
			$whereSql = "$column_name='" . $change_all . "'";
		} else {
			$whereSql = "Ticket_Id='" . $Ticket_Id . "'";
		}
		return $autobatch->query("update TICKET set $column_name='" . $JO_Number . "' where $whereSql");
	}
	public function updateJoNumber_eurotech($sheet_no, $JO_Number, $change_all)
	{
		return false;
	}

	///////////////////////////////////////////////////////////////////////////////////////////
	// 									TOTAL RECORD										 //
	///////////////////////////////////////////////////////////////////////////////////////////
	public function totalRecord($id_region, $tglStart = '', $tglEnd = '', $search = '', $BP_ID = '', $ada_jo, $ada_sklp, $mesin = 'commandbatch')
	{
		if ($mesin == 'commandbatch') {
			return $this->totalRecord_commandbatch($id_region, $tglStart, $tglEnd, $search, $BP_ID, $ada_jo, $ada_sklp);
		} else if ($mesin == 'autobatch') {
			return $this->totalRecord_autobatch($id_region, $tglStart, $tglEnd, $search, $BP_ID);
		} else if ($mesin == 'eurotech') {
			return $this->totalRecord_eurotech($id_region, $tglStart, $tglEnd, $search, $BP_ID);
		}
	}

	public function totalRecord_commandbatch($id_region, $tglStart = '', $tglEnd = '', $search = '', $BP_ID = '', $ada_jo, $ada_sklp)
	{
		// record date
		if ($tglStart == '' && $tglEnd == '') {
			$record_date = " and a.RecordDate BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() + INTERVAL 1 DAY ";
		} else {
			if ($tglStart == '') {
				$tglStart = "2018-01-01 00:00";
			}
			if ($tglEnd == '') {
				$tglEnd = "CURDATE() + INTERVAL 1 DAY";
			} else {
				$tglEnd = "'$tglEnd'";
			}

			$record_date = " and a.RecordDate BETWEEN '" . $tglStart . "' AND " . $tglEnd . " ";
		}
		// record date


		$having = is_numeric($search) ? " having (index_load like '%$search%' or max_ticket like '%$search%' or ROUND(Ordered_Qty, 2) like '%$search%' or total_load like '%$search%' or total_batch like '%$search%' 
										or ROUND(Delivered_Qty,2) like '%$search%' ) " : '';
		$search = ($search != '' && !is_numeric($search)) ? " and CONCAT_WS(' ', a.Delivery_Instruction, a.Item_Code, a.Customer_Description, b.bp_name) like '%$search%' " : '';

		$BP_ID = $BP_ID != '' ? " and a.BP_ID=$BP_ID " : '';

		$ada_jo = $ada_jo ? ' and (a.Delivery_Instruction is not null) ' : '';
		$ada_sklp = $ada_sklp ? ' and ((a.Job_Code is not null) or (a.PO_Num is not null)) ' : '';

		$group_by = " group by 
					CASE WHEN OrderID IS NOT NULL 
					THEN a.OrderID
					ELSE a.Delivery_Instruction
					END
					, a.Item_Code, a.BP_ID ";
		$group_by = "";

		$query = "	select COUNT(*) as total FROM(
						select a.index_load, a.OrderID, a.Ticket_code max_ticket, 
							
							case when (a.OrderID is not null and a.OrderID !='') 
								then a.Ordered_Qty 
								else a.Price_Qty 
							end Ordered_Qty, 
							
							a.Delivery_Instruction, a.Item_Code, a.Customer_Description, a.RecordDate as tanggal, 1 as total_load, a.Max_Batch as total_batch, 
							
							case when (a.OrderID is not null and a.OrderID !='') 
								then a.Delivered_Qty
								else a.Load_Size
							end Delivered_Qty, 

							a.Ticket_Status, a.BP_ID, b.bp_name, c.region_name
						from `V_BatchSetupTickets` a
						left join `Batching_plant` b on b.id_bp = a.BP_ID
						left join `Region` c on c.id_region = b.id_region
						where 1 and (a.Ticket_Status ='C' or a.Ticket_Status ='O') and c.id_region = $id_region $record_date $search $BP_ID $ada_jo $ada_sklp 
						$group_by 
						$having 
					) x";

		$result = $this->db->query($query)->result();
		return $result[0]->total;
	}

	public function totalRecord_autobatch($id_region, $tglStart = '', $tglEnd = '', $search = '', $BP_ID = '')
	{
		$autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.

		// record date
		if ($tglStart == '' && $tglEnd == '') {
			$record_date = " and y.tanggal BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() + INTERVAL 1 DAY ";
		} else {
			if ($tglStart == '') {
				$tglStart = "2018-01-01 00:00";
			}
			if ($tglEnd == '') {
				$tglEnd = "CURDATE() + INTERVAL 1 DAY";
			} else {
				$tglEnd = "'$tglEnd'";
			}

			$record_date = " and y.tanggal BETWEEN '" . $tglStart . "' AND " . $tglEnd . " ";
		}
		// record date

		$having = is_numeric($search) ? " having (max_ticket like '%$search%' or ROUND(Ordered_Qty, 2) like '%$search%' or total_load like '%$search%' or total_batch like '%$search%' 
										or ROUND(Delivered_Qty,2) like '%$search%' ) " : '';
		$search = ($search != '' && !is_numeric($search)) ? " and CONCAT_WS(' ', Delivery_Instruction, Item_Code, Customer_Description, bp_name) like '%$search%' " : '';

		$BP_ID = $BP_ID != '' ? " and a.BP_ID=$BP_ID " : '';

		// $query = 	"select COUNT(*) as total FROM(
		// 				select x.total_batch total_batch, x.Delivered_Qty Delivered_Qty, y.tanggal tanggal,
		// 				y.max_ticket max_ticket, y.Delivery_Instruction, y.Jobmix_Id, y.Item_Code, y.Ordered_Qty Ordered_Qty, y.BP_ID, y.total_load total_load, y.BP_Name bp_name,
		// 				y.Customer_Description, y.OrderID, y.Ticket_Status
		// 				from (
		// 					select Ticket_Id max_ticket, a.Remarks Delivery_Instruction, a.Jobmix_Id, b.Jobmix_Code Item_Code, a.Qty_Jobmix Ordered_Qty, a.BP_ID, 1 total_load, a.BP_Name bp_name, a.Createdate tanggal,
		// 					'Adhi Beton' Customer_Description, '' OrderID, 'O' Ticket_Status
		// 					from TICKET a inner join JOBMIX_HEADER b on a.Jobmix_Id = b.Jobmix_Id 
		// 					inner join Batching_plant bp on a.BP_ID = bp.id_bp 
		// 					where bp.id_region = $id_region $BP_ID
		// 					) y
		// 				inner join (
		// 					select a.Ticket_Id, count(a.Ticket_Id) total_batch, sum(a.Qty_Jobmix) Delivered_Qty
		// 					FROM BATCH_HEADER a 
		// 					inner join Batching_plant bp on a.BP_ID = bp.id_bp 
		// 					where bp.id_region = $id_region $BP_ID
		// 					group by Ticket_Id
		// 				) x on x.Ticket_Id = y.max_ticket
		// 				where 1 $record_date $search	
		// 				$having				
		// 			) x";
		$query = "	select count(z.index_load) as total
						from (
							select  
								x.total_batch total_batch, x.Delivered_Qty Load_Size, y.tanggal tanggal, y.PO_Number sklp,
								y.max_ticket max_ticket, y.Delivery_Instruction, y.Jobmix_Id, y.Item_Code, y.Ordered_Qty Ordered_Qty, y.BP_ID, y.total_load total_load, y.BP_Name bp_name,
								y.Customer_Description, y.OrderID, y.Ticket_Status, y.index_load
							from (
								select index_load, Ticket_Id max_ticket, PO_Number, a.Jobmix_Id, b.Jobmix_Code Item_Code, a.Qty_Jobmix Ordered_Qty, a.BP_ID, 1 total_load, a.BP_Name bp_name, a.Createdate tanggal,
								
								case
									when bp.JO_Column='Jo_Number' then a.Jo_Number
									when bp.JO_Column='PO_Number' then a.PO_Number
									when bp.JO_Column='Remarks' then a.Remarks
								end Delivery_Instruction,

								'Adhi Beton' Customer_Description, '' OrderID, 'O' Ticket_Status
								from TICKET a inner join JOBMIX_HEADER b on a.Jobmix_Id = b.Jobmix_Id 
								inner join Batching_plant bp on a.BP_ID = bp.id_bp 
								where bp.id_region = $id_region $BP_ID
								) y
							inner join (
								select a.Ticket_Id, count(a.Ticket_Id) total_batch, sum(a.Qty_Jobmix) Delivered_Qty
								FROM BATCH_HEADER a 
								inner join Batching_plant bp on a.BP_ID = bp.id_bp 
								where bp.id_region = $id_region $BP_ID
								group by Ticket_Id
							) x on x.Ticket_Id = y.max_ticket
							where 1 $record_date $search
							$having 
						) z";
		$result = $autobatch->query($query)->result();
		return $result[0]->total;
	}

	public function totalRecord_eurotech($id_region, $tglStart = '', $tglEnd = '', $search = '', $BP_ID = '')
	{
		$eurotech = $this->load->database('eurotech', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.

		// record date
		if ($tglStart == '' && $tglEnd == '') {
			$record_date = " and Sheet.date_mix BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() + INTERVAL 1 DAY ";
		} else {
			if ($tglStart == '') {
				$tglStart = "2018-01-01 00:00";
			}
			if ($tglEnd == '') {
				$tglEnd = "CURDATE() + INTERVAL 1 DAY";
			} else {
				$tglEnd = "'$tglEnd'";
			}

			$record_date = " and Sheet.date_mix BETWEEN '" . $tglStart . "' AND " . $tglEnd . " ";
		}
		// record date

		$having = is_numeric($search) ? " having (max_ticket like '%$search%' or ROUND(Ordered_Qty, 2) like '%$search%' or total_load like '%$search%' or total_batch like '%$search%' 
										or ROUND(Delivered_Qty,2) like '%$search%' ) " : '';
		$search = ($search != '' && !is_numeric($search)) ? " and CONCAT_WS(' ', order_description01, receipe_code, customer, bp_name) like '%$search%' " : '';

		$BP_ID = $BP_ID != '' ? " and Sheet.bp_id=$BP_ID " : '';

		$query = "select count(*) as total from(
					select delivery_no_order as max_ticket, order_description01 as Delivery_Instruction, order_description02 as sklp,'' as Item_Code, m3_delivered Ordered_Qty, m3_delivery as Delivered_Qty, m3_delivery as Load_Size, customer as Customer_Description, date_end as tanggal, 1 as total_load, batch_total as total_batch, bp_id as BP_ID, Batching_plant.bp_name as bp_name
					FROM `Sheet` 
					inner join Batching_plant on Batching_plant.id_bp = Sheet.bp_id 
					where Batching_plant.id_region = $id_region $BP_ID $record_date $search
					$having
					) x";
		$result = $eurotech->query($query)->result();
		return $result[0]->total;
	}
}
