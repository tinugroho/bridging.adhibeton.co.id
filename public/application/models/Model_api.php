<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_api extends CI_Model
{
    public function index($id = null, $length = '', $offset = '', $search = '', $order = '')
    {
    }

    public function active()
    {
        // $query = "SELECT `type`, `JO_Number`,`Item_Code`,`Item_Qty`,`Remaining_Qty`,`JO_Date`, `API_Date` FROM `API_Logs_Header` WHERE 1 ORDER BY `API_Logs_Header`.`JO_Date` DESC";
        // $query = "select `index_Get`, `type`, `Created_By`, `Formula_Number`, `Item_Qty`, `JO_Number`, `SIP_Number`, `Created_Date`, `Item_Code`, `Item_Name`, `JO_Reference`, `Last_Update`, `MR_Number`, `Remaining_Qty`, `Updated_By`, `Estimated_TimeProduction`, `Material_Name`, `JO_Date`, `Material_Code`, max(`API_Date`) API_Date, db 
        //         from 
        //         (
        //             (SELECT *, 'autobatch' as db from db_autobatch.API_Logs_Header)
        //             union
        //             (SELECT *, 'commandbatch' as db from db_bridging.API_Logs_Header)
        //             order by JO_Number, type, API_Date DESC
        //         ) as x
        //         group by JO_Number, type
        //         order by JO_Date desc";
        $query = "select `index_Get`, `type`, `Created_By`, `Formula_Number`, `Item_Qty`, `JO_Number`, `SIP_Number`, `Created_Date`, `Item_Code`, `Item_Name`, `JO_Reference`, `Last_Update`, `MR_Number`, `Remaining_Qty`, `Updated_By`, `Estimated_TimeProduction`, `Material_Name`, `JO_Date`, `Material_Code`, max(`API_Date`) API_Date, db,
                    (
                        (
                            SELECT count(a.Index_Log) x 
                            FROM `API_Logs_Detail` a 
                            inner join V_BatchSetupTickets b on a.Index_Load=b.index_load
                            inner join Batching_plant c on b.BP_ID=c.id_bp
                            WHERE a.`Method`='POST' and a.`type`= x.type and a.`JO_Number`= x.JO_Number
                                and c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")
                        ) 
                        +
                        (
                            SELECT count(a.Index_Log) x 
                            FROM db_autobatch.API_Logs_Detail a
                            inner join db_autobatch.TICKET b on a.Index_Load=b.Ticket_Id                            
                            inner join db_autobatch.Batching_plant c on b.BP_ID=c.id_bp
                            WHERE a.`Method`='POST' and a.`type`= x.type and a.`JO_Number`= x.JO_Number
                                and c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")
                        ) 
                    ) post_sukses,
                    (
                        (
                            SELECT count(a.Index_Post_Gagal) x 
                            FROM `API_Post_Gagal` a 
                            inner join V_BatchSetupTickets b on a.Index_Load=b.index_load
                            inner join Batching_plant c on b.BP_ID=c.id_bp
                            WHERE a.`type`= x.type and a.`JO_Number`= x.JO_Number
                                and c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")
                        ) 
                        +
                        (
                            SELECT count(a.Index_Post_Gagal) x 
                            FROM db_autobatch.API_Post_Gagal a 
                            inner join db_autobatch.TICKET b on a.Index_Load=b.Ticket_Id                            
                            inner join db_autobatch.Batching_plant c on b.BP_ID=c.id_bp
                            WHERE a.`type`= x.type and a.`JO_Number`= x.JO_Number
                                and c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")
                        ) 
                    ) post_gagal

                from 
                (
                    (SELECT *, 'autobatch' as db from db_autobatch.API_Logs_Header)
                    union
                    (SELECT *, 'commandbatch' as db from db_bridging.API_Logs_Header)
                    order by JO_Number, type, API_Date DESC
                ) as x
                group by JO_Number, type
                order by JO_Date desc";
        // echo $query;
        $result = $this->db->query($query)->result();
        // foreach ($result as $key => $val) {
        //     $result[$key]->type = $val->type;

        //     $result[$key]->post_sukses = $this->Model_api->countPostSukses($val->type, $val->JO_Number);
        //     $result[$key]->post_gagal = $this->Model_api->countPostGagal($val->type, $val->JO_Number);
        // }
        return $result;
    }

    public function countPostSukses($type, $JO_Number)
    {
        $db_autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.

        $query_commandbatch = " SELECT count(a.Index_Log) x FROM `API_Logs_Detail` a 
                                inner join V_BatchSetupTickets b on a.Index_Load=b.index_load
                                inner join Batching_plant c on b.BP_ID=c.id_bp
                                WHERE a.`Method`='POST' and a.`type`='$type' and a.`JO_Number`='$JO_Number' 
                                    and c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")";
        $query_autobatch = "SELECT count(a.Index_Log) x FROM `API_Logs_Detail`  a
                            inner join TICKET b on a.Index_Load=b.Ticket_Id                            
                            inner join Batching_plant c on b.BP_ID=c.id_bp
                            WHERE a.`Method`='POST' and a.`type`='$type' and a.`JO_Number`='$JO_Number' 
                                and c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")";

        $commandbatch = $this->db->query($query_commandbatch)->result();
        $commandbatch = $commandbatch[0]->x;

        $autobatch = $db_autobatch->query($query_autobatch)->result();
        $autobatch = $autobatch[0]->x;


        return $commandbatch + $autobatch;
    }

    public function countPostGagal($type, $JO_Number)
    {
        $db_autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.

        $query_commanbatch = "  SELECT count(a.Index_Post_Gagal) x 
                                FROM `API_Post_Gagal` a 
                                inner join V_BatchSetupTickets b on a.Index_Load=b.index_load
                                inner join Batching_plant c on b.BP_ID=c.id_bp
                                WHERE a.`type`='$type' and a.`JO_Number`='$JO_Number' 
                                    and c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")";
        $query_autobatch = "SELECT count(a.Index_Post_Gagal) x 
                            FROM `API_Post_Gagal` a 
                            inner join TICKET b on a.Index_Load=b.Ticket_Id                            
                            inner join Batching_plant c on b.BP_ID=c.id_bp
                            WHERE a.`type`='$type' and a.`JO_Number`='$JO_Number' 
                                and c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")";

        $commandbatch = $this->db->query($query_commanbatch)->result();
        $commandbatch = $commandbatch[0]->x;

        $autobatch = $db_autobatch->query($query_autobatch)->result();
        $autobatch = $autobatch[0]->x;

        return $commandbatch + $autobatch;
    }

    public function postSukses($type, $JO_Number)
    {
        // $query = "SELECT * FROM `API_Logs_Detail` WHERE `Method`='POST' and `type`='$type' and `JO_Number`='$JO_Number' order by Method_Date desc";
        // $query = "  select * from 
        //                 ((  SELECT a.*, 
        //                         d.region_name, 'commandbatch' as db FROM db_bridging.API_Logs_Detail a 
        //                     inner join db_bridging.V_BatchSetupTickets b on a.Index_Load=b.index_load
        //                     inner join db_bridging.Batching_plant c on b.BP_ID=c.id_bp
        //                     inner join db_bridging.Region d on c.id_region=d.id_region
        //                     WHERE a.`Method`='POST' and a.`type`='$type' and a.`JO_Number`='$JO_Number' and c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")
        //                     order by a.Method_Date desc)
        //                 union
        //                 (   SELECT a.*, 
        //                         d.region_name, 'autobatch' as db 
        //                     FROM db_autobatch.API_Logs_Detail a
        //                     inner join db_autobatch.TICKET b on a.Index_Load=b.Ticket_Id
        //                     inner join db_autobatch.Batching_plant c on b.BP_ID=c.id_bp
        //                     inner join db_autobatch.Region d on c.id_region=d.id_region
        //                     WHERE a.`Method`='POST' and a.`type`='$type' and a.`JO_Number`='$JO_Number' and c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")
        //                     order by a.Method_Date desc)) x
        //             order by Method_Date desc";
        $query = "  select * from 
                        ((  SELECT a.Jobmix_ERP, a.Jobmix_BP, a.Material, a.Post_Qty, a.CreateDateBP, a.Method_Date, a.Index_Load, a.Keterangan, a.type, a.Index_Log,
                                d.region_name, 'commandbatch' as db FROM db_bridging.API_Logs_Detail a 
                            inner join db_bridging.V_BatchSetupTickets b on a.Index_Load=b.index_load
                            inner join db_bridging.Batching_plant c on b.BP_ID=c.id_bp
                            inner join db_bridging.Region d on c.id_region=d.id_region
                            WHERE a.`Method`='POST' and a.`type`='$type' and a.`JO_Number`='$JO_Number' and c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")
                            order by a.Method_Date desc)
                        union
                        (   SELECT a.Jobmix_ERP, a.Jobmix_BP, a.Material, a.Post_Qty, a.CreateDateBP, a.Method_Date, a.Index_Load, a.Keterangan, a.type, a.Index_Log,
                                d.region_name, 'autobatch' as db 
                            FROM db_autobatch.API_Logs_Detail a
                            inner join db_autobatch.TICKET b on a.Index_Load=b.Ticket_Id
                            inner join db_autobatch.Batching_plant c on b.BP_ID=c.id_bp
                            inner join db_autobatch.Region d on c.id_region=d.id_region
                            WHERE a.`Method`='POST' and a.`type`='$type' and a.`JO_Number`='$JO_Number' and c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")
                            order by a.Method_Date desc)) x
                    order by Method_Date desc";
        return $this->db->query($query)->result();
    }
    public function detailPostSukses($Index_Log, $mesin = 'commandbatch')
    {
        $db_autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.


        if ($mesin == 'commandbatch') {
            $query = "SELECT * FROM `API_Logs_Detail` WHERE `Index_Log`='$Index_Log'";
            return $this->db->query($query)->result();
        } else if ($mesin == 'autobatch') {
            $query = "SELECT *, index_load as Index_Load FROM `API_Logs_Detail` WHERE `Index_Log`='$Index_Log'";
            return $db_autobatch->query($query)->result();
        }
    }

    public function postGagal($type, $JO_Number)
    {
        // $query = "select * from
        //             ((  SELECT a.*, b.Ticket_Code as Ticket_Id, c.bp_name, d.region_name, 'commandbatch' as db FROM db_bridging.API_Post_Gagal a 
        //                 inner join db_bridging.V_BatchSetupTickets b on a.Index_Load=b.index_load
        //                 inner join db_bridging.Batching_plant c on b.BP_ID=c.id_bp
        //                 inner join db_bridging.Region d on c.id_region=d.id_region
        //                 WHERE a.`type`='$type' and a.`JO_Number`='$JO_Number'  and c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")
        //                 order by a.Running_Date desc)
        //             union
        //             (   SELECT a.*, c.bp_name, d.region_name, 'autobatch' as db FROM db_autobatch.API_Post_Gagal a 
        //                 inner join db_autobatch.TICKET b on a.Index_Load=b.Ticket_Id
        //                 inner join db_autobatch.Batching_plant c on b.BP_ID=c.id_bp
        //                 inner join db_autobatch.Region d on c.id_region=d.id_region
        //                 WHERE a.`type`='$type' and a.`JO_Number`='$JO_Number'  and c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")
        //                 order by a.Running_Date desc)) x
        //         order by Running_Date desc";
        $query = "select * from
                    ((  SELECT a.Index_Post_Gagal, a.type, a.Jobmix_ERP, a.Jobmix_BP, a.Material, a.Load_Size, a.RecordDate, a.Running_Date, a.Index_Load, a.Keterangan, b.Ticket_Code as Ticket_Id,
                            c.bp_name, d.region_name, 'commandbatch' as db FROM db_bridging.API_Post_Gagal a 
                        inner join db_bridging.V_BatchSetupTickets b on a.Index_Load=b.index_load
                        inner join db_bridging.Batching_plant c on b.BP_ID=c.id_bp
                        inner join db_bridging.Region d on c.id_region=d.id_region
                        WHERE a.`type`='$type' and a.`JO_Number`='$JO_Number'  and c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")
                        order by a.Running_Date desc)
                    union
                    (   SELECT a.Index_Post_Gagal, a.type, a.Jobmix_ERP, a.Jobmix_BP, a.Material, a.Load_Size, a.RecordDate, a.Running_Date, a.index_load as Index_Load, a.Keterangan, a.Ticket_Id,
                            c.bp_name, d.region_name, 'autobatch' as db FROM db_autobatch.API_Post_Gagal a 
                        inner join db_autobatch.TICKET b on a.Index_Load=b.Ticket_Id
                        inner join db_autobatch.Batching_plant c on b.BP_ID=c.id_bp
                        inner join db_autobatch.Region d on c.id_region=d.id_region
                        WHERE a.`type`='$type' and a.`JO_Number`='$JO_Number'  and c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")
                        order by a.Running_Date desc)) x
                order by Running_Date desc";
        return $this->db->query($query)->result();
    }
    public function detailPostGagal($Index_Post_Gagal, $mesin = 'commandbatch')
    {

        if ($mesin == 'commandbatch') {
            $query = "SELECT * FROM `API_Post_Gagal` WHERE `Index_Post_Gagal`='$Index_Post_Gagal'";
            return $this->db->query($query)->result();
        } else if ($mesin == 'autobatch') {
            $db_autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
            $query = "SELECT *, index_load 'Index_Load' FROM `API_Post_Gagal` WHERE `Index_Post_Gagal`='$Index_Post_Gagal'";
            return $db_autobatch->query($query)->result();
        }
    }
    public function detailLoad($Index_Load, $mesin = 'commandbatch')
    {
        $db_autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.

        if ($mesin == 'commandbatch') {
            $query = "  SELECT V_BatchSetupTickets.*, V_BatchSetupTickets.index_load Index_Load, Batching_plant.bp_name, Region.region_name 
                        FROM V_BatchSetupTickets, Batching_plant, Region 
                        WHERE V_BatchSetupTickets.BP_ID=Batching_plant.id_bp and Batching_plant.id_region=Region.id_region and V_BatchSetupTickets.Index_Load='$Index_Load'";
            return $this->db->query($query)->result();
        } else if ($mesin == 'autobatch') {
            $query = "
                SELECT 
                    a.*, a.index_load 'Index_Load', a.Ticket_Id Ticket_Code, a.Qty_Jobmix Load_Size, a.Driver Driver_Code, a.Truck Truck_Code, a.Createdate RecordDate, a.Createby CreatedBy, 
                    case
                        when b.JO_Column='Jo_Number' then a.Jo_Number
                        when b.JO_Column='PO_Number' then a.PO_Number
                        when b.JO_Column='Remarks' then a.Remarks
                    end Jo_Number_by_column,
                    b.bp_name, c.region_name, d.Jobmix_Code Item_Code 
                FROM `TICKET` a, Batching_plant b, Region c, JOBMIX_HEADER d
                WHERE a.BP_ID=b.id_bp and b.id_region=c.id_region and a.Jobmix_Id=d.Jobmix_Id and a.index_load='$Index_Load'";
            return $db_autobatch->query($query)->result();
        }
    }
    public function repost($type, $Index_Post_Gagal, $Input_JO_Number, $Input_Material, $mesin = 'commandbatch')
    {
        $arr_input_material = explode('-', $Input_Material);
        $arr_material_akumulasi = [];
        foreach ($arr_input_material as $pasangan_material) {
            $arr_pasangan_material = explode('|', $pasangan_material);
            if (isset($arr_material_akumulasi[$arr_pasangan_material[0]])) {
                $arr_material_akumulasi[$arr_pasangan_material[0]] = $arr_material_akumulasi[$arr_pasangan_material[0]] + $arr_pasangan_material[1];
            } else {
                $arr_material_akumulasi[$arr_pasangan_material[0]] = $arr_pasangan_material[1];
            }
        }

        $material_akumulasi_str = [];
        foreach ($arr_material_akumulasi as $x => $y) {
            $material_akumulasi_str[] = $x . '|' . $y;
        }
        $material_akumulasi_str = implode('-', $material_akumulasi_str);

        $db_autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.

        $strtype = $type;
        $detailPostGagal = $this->detailPostGagal($Index_Post_Gagal, $mesin);
        $detailPostGagal = $detailPostGagal[0];
        // var_dump($detailPostGagal);
        $RecordDate = date_format(date_create($detailPostGagal->RecordDate), 'Y-m-d');
        $type = explode('-', $type);
        $devOrLive = $type[0] == 'live' ? '' : 'dev';
        if ($type[1] == 'bspi') {
            $url = "http://192.168.100.17/apberp" . $devOrLive . "/erp/eaccounting/tools/api/sfservice.cfc?method=setDataBSPI&JO_Number=" . $Input_JO_Number . "&Reference=" . $detailPostGagal->Index_Load . "&Trx_Date=" . $RecordDate . "&Ship_Distance=1&Output_Qty=" . $detailPostGagal->Load_Size . "&Input_Qty=" . $material_akumulasi_str;
        } else {
            $url = "http://192.168.100.17/apberp" . $devOrLive . "/erp/eaccounting/tools/api/sfservice.cfc?method=setDataBSP&JO_Number=" . $Input_JO_Number . "&SO_Number=" . $detailPostGagal->SO_Number . "&Reference=" . $detailPostGagal->Index_Load . "&Trx_Date=" . $RecordDate . "&Ship_Distance=1&Output_Qty=" . $detailPostGagal->Load_Size . "&Input_Qty=" . $material_akumulasi_str . "&Driver=" . $detailPostGagal->Driver_Code . "&Vehicle_No=" . $detailPostGagal->Truck_Code . "&Trx_ID=" . $detailPostGagal->Ticket_Code;
        }
        $url = str_replace(" ", "%20", $url);
        // echo $url;
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

        //POST SUKSES
        if (is_array($result) && isset($result['return']) && $result['return'] == 1) {
            // UPDATE STATUS POST
            $query = "UPDATE `API_Post_Gagal` SET `Keterangan`= 'POSTED', JO_Number='$Input_JO_Number', Material='$Input_Material', jml_post = jml_post + 1, Running_Date = NOW() 
					where Index_Post_Gagal = " . $Index_Post_Gagal;
            if ($mesin == 'commandbatch') {
                $this->db->query($query);
            } else if ($mesin == 'autobatch') {
                $db_autobatch->query($query);
            }


            // API LOGS HEADER
            $query = "update `API_Logs_Header` SET `Remaining_Qty`= (Remaining_Qty-" . $detailPostGagal->Load_Size . ") WHERE JO_Number='" . $Input_JO_Number . "'";
            if ($mesin == 'commandbatch') {
                $this->db->query($query);
            } else if ($mesin == 'autobatch') {
                $db_autobatch->query($query);
            }

            $query = "select * from API_Logs_Header where JO_Number='$Input_JO_Number' and type='$strtype'";
            if ($mesin == 'commandbatch') {
                $x = $this->db->query($query)->result();
            } else if ($mesin == 'autobatch') {
                $x = $db_autobatch->query($query)->result();
            }
            $x = $x[0];

            //LOG POST
            $query = "INSERT INTO `API_Logs_Detail`(`type`, `Method`, `JO_Number`, `Jobmix_ERP`, `Jobmix_BP`, `Req_Qty`, `Remain_Qty`, `Post_Qty`, `BP`, `CreateByERP`, `CreateDateERP`, `UpdateByERP`, `UpdateDateERP`, CreateByBP, CreateDateBP, Regional,
                        `Material`, `Keterangan`, `Index_Load`) VALUES ('$strtype', 'POST', '" . $Input_JO_Number . "', '" . $detailPostGagal->Jobmix_ERP . "', '" . $detailPostGagal->Jobmix_BP . "', " . $x->Item_Qty . ", " . $x->Remaining_Qty . ", " . $detailPostGagal->Load_Size . ", '', '" . $x->Created_By . "', 
                        '" . $x->Created_Date . "', '" . $x->Updated_By . "', '" . $x->Last_Update . "', '', '" . $detailPostGagal->RecordDate . "', '', '" . $Input_Material . "', 'sukses', " . $detailPostGagal->Index_Load . ")";
            if ($mesin == 'commandbatch') {
                $this->db->query($query);
            } else if ($mesin == 'autobatch') {
                $db_autobatch->query($query);
            }

            // API FINISH
            if ($x->Remaining_Qty <= 0) {
                $query = "insert INTO `API_Finish`(`type`, `Created_By`, `Formula_Number`, `Item_Qty`, `JO_Number`, `SIP_Number`, `Created_Date`, `Item_Code`, `Item_Name`, `JO_Reference`,
                `Last_Update`, `MR_Number`, `Remaining_Qty`, `Updated_By`, `Estimated_TimeProduction`, `Material_Name`, `JO_Date`, `Material_Code`) VALUES ('" . $strtype . "', '" . $x->Created_By . "', 
                '" . $x->Formula_Number . "', " . $x->Item_Qty . ", '" . $x->JO_Number . "', '" . $x->SIP_Number . "', '" . $x->Created_Date . "', '" . $x->Item_Code . "', '" . $x->Item_Name . "', 
                '" . $x->JO_Reference . "', '" . $x->Last_Update . "', '" . $x->MR_Number . "', " . $x->Remaining_Qty . ", '" . $x->Updated_By . "', '" . $x->Estimated_TimeProduction . "', '" . $x->Material_Name . "', 
                '" . $x->JO_Date . "', '" . $x->Material_Code . "')";
                if ($mesin == 'commandbatch') {
                    $this->db->query($query);
                } else if ($mesin == 'autobatch') {
                    $db_autobatch->query($query);
                }
            }

            return $this->detailPostGagal($Index_Post_Gagal, $mesin)[0];
        } else {
            $query = "update `API_Post_Gagal` set  Material='$Input_Material', `Keterangan`= '" . json_encode($result) . "', jml_post = jml_post + 1, Running_Date = NOW() 
            where Index_Post_Gagal = " . $Index_Post_Gagal;
            if ($mesin == 'commandbatch') {
                $this->db->query($query);
            } else if ($mesin == 'autobatch') {
                $db_autobatch->query($query);
            }
            $detailPostGagal->Keterangan = json_encode($result);
            $detailPostGagal->Material = $Input_Material;
            return $detailPostGagal;
        }
    }
    public function post($type, $Index_Load, $JO_Number, $data_jo, $Input_Material, $mesin = 'commandbatch')
    {
        $arr_input_material = explode('-', $Input_Material);
        $arr_material_akumulasi = [];
        foreach ($arr_input_material as $pasangan_material) {
            $arr_pasangan_material = explode('|', $pasangan_material);
            if (isset($arr_material_akumulasi[$arr_pasangan_material[0]])) {
                $arr_material_akumulasi[$arr_pasangan_material[0]] = $arr_material_akumulasi[$arr_pasangan_material[0]] + $arr_pasangan_material[1];
            } else {
                $arr_material_akumulasi[$arr_pasangan_material[0]] = $arr_pasangan_material[1];
            }
        }

        $material_akumulasi_str = [];
        foreach ($arr_material_akumulasi as $x => $y) {
            $material_akumulasi_str[] = $x . '|' . $y;
        }
        $material_akumulasi_str = implode('-', $material_akumulasi_str);

        $db_autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.

        $strtype = $type;
        $detailLoad = $this->detailLoad($Index_Load, $mesin);
        $detailLoad = $detailLoad[0];
        // var_dump($detailLoad);
        $RecordDate = date_format(date_create($detailLoad->RecordDate), 'Y-m-d');
        $type = explode('-', $type);
        $devOrLive = $type[0] == 'live' ? '' : 'dev';
        if ($type[1] == 'bspi') {
            $url = "http://192.168.100.17/apberp" . $devOrLive . "/erp/eaccounting/tools/api/sfservice.cfc?method=setDataBSPI&JO_Number=" . $JO_Number . "&Reference=" . $detailLoad->index_load . "&Trx_Date=" . $RecordDate . "&Ship_Distance=1&Output_Qty=" . $detailLoad->Load_Size . "&Input_Qty=" . $material_akumulasi_str;
        } else {
            $url = "http://192.168.100.17/apberp" . $devOrLive . "/erp/eaccounting/tools/api/sfservice.cfc?method=setDataBSP&JO_Number=" . $JO_Number . "&SO_Number=" . $data_jo['SO_Number'] . "&Reference=" . $detailLoad->index_load . "&Trx_Date=" . $RecordDate . "&Ship_Distance=1&Output_Qty=" . $detailLoad->Load_Size . "&Input_Qty=" . $material_akumulasi_str . "&Driver=" . $detailLoad->Driver_Code . "&Vehicle_No=" . $detailLoad->Truck_Code . "&Trx_ID=" . $detailLoad->Ticket_Code;
        }
        $url = str_replace(" ", "%20", $url);
        // var_dump($url);
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

        //POST SUKSES
        if (is_array($result) && isset($result['return']) && $result['return'] == 1) {

            // INSERT TO API DETAIL
            $Created_Date = !empty($data_jo['Created_Date']) ? "'" . $data_jo['Created_Date'] . "'" : "NULL";
            $Last_Update = !empty($data_jo['Last_Update']) ? "'" . $data_jo['Last_Update'] . "'" : "NULL";

            $query = "INSERT INTO `API_Logs_Detail` 
                        (`type`, `Method`, `JO_Number`, `Jobmix_ERP`, `Jobmix_BP`, `Req_Qty`, 
                        `Remain_Qty`, `Post_Qty`, `BP`, `CreateByERP`, 
                        `CreateDateERP`, `UpdateByERP`, `UpdateDateERP`, CreateByBP, CreateDateBP, Regional, `Material`, 
                        `Keterangan`, `Index_Load`) 
                        VALUES 
                        ('$strtype', 'POST', '" . $JO_Number . "', '" . $data_jo['Item_Code'] . "', '" . $detailLoad->Item_Code . "', " . $data_jo['Item_Qty'] . ", 
                        " . ($data_jo['Remaining_Qty'] - $detailLoad->Load_Size) . ", " . $detailLoad->Load_Size . ", '" . $detailLoad->bp_name . "', '" . $data_jo['Created_By'] . "', 
                        " . $Created_Date . ", '" . $data_jo['Updated_By'] . "', " . $Last_Update . ", '" . $detailLoad->CreatedBy . "', '" . $RecordDate . "', '" . $detailLoad->region_name . "', '" . $Input_Material . "', 
                        'sukses', " . $Index_Load . ")";
            // echo $query;
            if ($mesin == 'commandbatch') {
                $this->db->query($query);
            } else if ($mesin == 'autobatch') {
                $db_autobatch->query($query);
            }


            // API LOGS HEADER
            $query = "update `API_Logs_Header` SET `Remaining_Qty`= (Remaining_Qty-" . $detailLoad->Load_Size . ") WHERE JO_Number='" . $JO_Number . "'";
            if ($mesin == 'commandbatch') {
                $this->db->query($query);
            } else if ($mesin == 'autobatch') {
                $db_autobatch->query($query);
            }

            $query = "select * from API_Logs_Header where JO_Number='$JO_Number' and type='$strtype'";
            if ($mesin == 'commandbatch') {
                $x = $this->db->query($query)->result();
            } else if ($mesin == 'autobatch') {
                $x = $db_autobatch->query($query)->result();
            }
            if (!empty($x)) {
                $x = $x[0];

                //     // API FINISH
                if ($x->Remaining_Qty <= 0) {
                    $query = "insert INTO `API_Finish`(`type`, `Created_By`, `Formula_Number`, `Item_Qty`, `JO_Number`, `SIP_Number`, `Created_Date`, `Item_Code`, `Item_Name`, `JO_Reference`,
                `Last_Update`, `MR_Number`, `Remaining_Qty`, `Updated_By`, `Estimated_TimeProduction`, `Material_Name`, `JO_Date`, `Material_Code`) VALUES ('" . $strtype . "', '" . $x->Created_By . "', 
                '" . $x->Formula_Number . "', " . $x->Item_Qty . ", '" . $x->JO_Number . "', '" . $x->SIP_Number . "', '" . $x->Created_Date . "', '" . $x->Item_Code . "', '" . $x->Item_Name . "', 
                '" . $x->JO_Reference . "', '" . $x->Last_Update . "', '" . $x->MR_Number . "', " . $x->Remaining_Qty . ", '" . $x->Updated_By . "', '" . $x->Estimated_TimeProduction . "', '" . $x->Material_Name . "', 
                '" . $x->JO_Date . "', '" . $x->Material_Code . "')";
                    if ($mesin == 'commandbatch') {
                        $this->db->query($query);
                    } else if ($mesin == 'autobatch') {
                        $db_autobatch->query($query);
                    }
                }
            }

            $detailLoad = $this->detailLoad($Index_Load, $mesin)[0];
            $detailLoad->Keterangan = 'sukses';
            return $detailLoad;
        } else {
            $query = "	INSERT INTO `API_Post_Gagal` 
                        (`type`, `JO_Number`, `Jobmix_ERP`, `Jobmix_BP`, `Index_Load`, 
                        `Load_Size`, `Material`, `SO_Number`, `Ticket_Code`, `Driver_Code`, `Truck_Code`,  `Keterangan`, `RecordDate`, `jml_post`,`bp_id`) 
						VALUES 
                        ('$strtype', '" . $JO_Number . "', '" . $data_jo['Item_Code'] . "', '" . $detailLoad->Item_Code . "', " . $Index_Load . ", 
                        " . $detailLoad->Load_Size . ", '" . $Input_Material . "', '" . $data_jo['SO_Number'] . "', $detailLoad->Ticket_Code, '$detailLoad->Driver_Code', '" . $detailLoad->Truck_Code . "', '" . json_encode($result) . "', '" . $RecordDate . "', 1, $detailLoad->BP_ID )";

            if ($mesin == 'commandbatch') {
                $this->db->query($query);
            } else if ($mesin == 'autobatch') {
                $db_autobatch->query($query);
            }
            $detailLoad->Keterangan = json_encode($result);
            $detailLoad->Material = $Input_Material;
            return $detailLoad;
        }
    }

    public function updatePostGagal($Index_Post_Gagal, $Input_JO_Number, $SO_Number, $Input_Material, $mesin = 'commandbatch')
    {
        if ($mesin == 'autobatch') {
            $db_autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
        }

        $query = "update `API_Post_Gagal` set  Material='$Input_Material', `JO_Number`= '" . $Input_JO_Number . "', SO_Number='$SO_Number' 
            where Index_Post_Gagal = " . $Index_Post_Gagal;
        if ($mesin == 'commandbatch') {
            $this->db->query($query);
        } else if ($mesin == 'autobatch') {
            $db_autobatch->query($query);
        }
        return $this->detailPostGagal($Index_Post_Gagal, $mesin)[0];
    }
    public function insertPostGagal($JO_Number, $data_jo, $type, $Str_Material, $detailLoad, $mesin = 'commandbatch')
    {
        if ($mesin == 'autobatch') {
            $db_autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
        }

        $query = "	INSERT INTO `API_Post_Gagal` 
                        (`type`, `JO_Number`, `Jobmix_ERP`, `Jobmix_BP`, `Index_Load`, 
                        `Load_Size`, `Material`, `SO_Number`, `Ticket_Code`, `Driver_Code`, `Truck_Code`,  `Keterangan`, `RecordDate`, `jml_post`,`bp_id`) 
						VALUES 
                        ('$type', '" . $JO_Number . "', '" . $data_jo['Item_Code'] . "', '" . $detailLoad->Item_Code . "', " . $detailLoad->Index_Load . ", 
                        " . $detailLoad->Load_Size . ", '" . $Str_Material . "', '" . $data_jo['SO_Number'] . "', $detailLoad->Ticket_Code, '$detailLoad->Driver_Code', '" . $detailLoad->Truck_Code . "', 'Update from manual post', '" . $detailLoad->RecordDate . "', 1, $detailLoad->BP_ID )";

        $insert_id = 0;
        if ($mesin == 'commandbatch') {
            $insert = $this->db->query($query);
            if ($insert) {
                $insert_id = $this->db->insert_id();
            }
        } else if ($mesin == 'autobatch') {
            $insert = $db_autobatch->query($query);
            if ($insert) {
                $insert_id = $db_autobatch->insert_id();
            }
        }
        return $insert_id;
    }
    public function deletePostGagal($Index_Post_Gagal, $mesin = 'commandbatch')
    {
        $query = "delete from API_Post_Gagal where Index_Post_Gagal=" . $Index_Post_Gagal;

        if ($mesin == 'commandbatch') {
            return $this->db->query($query);
        } else if ($mesin == 'autobatch') {
            $db_autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
            return $db_autobatch->query($query);
        }
    }

    public function materialPerLoad($index_load, $mesin = 'commandbatch')
    {
        if ($mesin == 'commandbatch') {
            return $this->materialPerLoad_commandbatch($index_load);
        } else if ($mesin == 'autobatch') {
            return $this->materialPerLoad_autobatch($index_load);
        }
    }
    public function materialPerLoad_commandbatch($index_load)
    {
        $LoadID = $this->db->query("select LoadID from V_BatchSetupTickets where index_load=" . $index_load)->result();
        $LoadID = (!empty($LoadID)) ? $LoadID[0]->LoadID : '';

        //SELECT MATERIAL PER LOADID
        $query = "select a.Item_Code, sum(a.Net_Auto_Batched_Amt) as Auto, a.Amt_UOM, b.Item_Code_ERP Alias
        from `Load_Lines` a 
        left join Item_Code_Alias b on a.Item_Code=b.Item_Code_BP
		where a.LoadID = '" . $LoadID . "'
		group by a.Item_Code
		order by a.Sort_Line_Num ASC";
        $materialPerLoad = $this->db->query($query)->result();

        // $materialPerLoad = array();
        // while ($material = mysqli_fetch_array($result)) {
        //     $materialPerLoad[] = $material;
        // }
        return $materialPerLoad;
    }
    public function materialPerLoad_autobatch($index_load)
    {
        $db_autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.

        $Ticket_Id = $db_autobatch->query("select Ticket_Id from TICKET where index_load=" . $index_load)->result();
        $Ticket_Id = (!empty($Ticket_Id)) ? $Ticket_Id[0]->Ticket_Id : '';

        //SELECT MATERIAL PER LOADID
        $query = "select a.Material_code Item_Code, sum(a.Actual_Qty) as Auto, a.Material_Uom Amt_UOM, b.Item_Code_ERP Alias 
        from `BATCH_DETAIL` a 
        left join Item_Code_Alias b on a.Material_code=b.Item_Code_BP
		where a.Ticket_Id = '" . $Ticket_Id . "'
		group by a.Material_code
		order by a.Material_code ASC";
        $materialPerLoad = $db_autobatch->query($query)->result();

        // $materialPerLoad = array();
        // while ($material = mysqli_fetch_array($result)) {
        //     $materialPerLoad[] = $material;
        // }

        return $materialPerLoad;
    }

    public function history($start, $end, $length, $offset, $search, $order)
    {
        // record date
        if ($start == '' && $end == '') {
            $Running_Date = " and Running_Date BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() + INTERVAL 1 DAY ";
        } else {
            if ($start == '') {
                $start = "2018-01-01 00:00";
            }
            if ($end == '') {
                $end = "CURDATE() + INTERVAL 1 DAY";
            } else {
                $end = "'$end'";
            }

            $Running_Date = " and Running_Date BETWEEN '" . $start . "' AND " . $end . " ";
        }
        // record date

        $length = $length != '' ? ' limit ' . $length : '';
        $offset = $offset != '' ? ' offset ' . $offset : '';

        $search = ($search != '') ? " and CONCAT_WS(' ', JO_Number, Jobmix_ERP, Jobmix_BP, Vol, RecordDate, Running_Date, Index_Load, Keterangan, status) like '%$search%' " : '';

        // order $order['column']-$order['dir']
        $order     = $order  != '' ? ' order by ' . $order['column'] . ' ' . $order['dir'] . ' ' : " order by Running_Date desc ";


        $query = "  select * FROM (                        
                        (   SELECT 'commandbatch' as db, a.`type`, a.`JO_Number`, a.`Jobmix_ERP`, a.`Jobmix_BP`, a.`Post_Qty` as `Vol`, a.`CreateDateBP` as `RecordDate`, a.`Method_Date` as `Running_Date`, a.`Index_Load`, a.`Keterangan`, 'sukses' as `status`, `Index_Log` 
                            FROM db_bridging.API_Logs_Detail a 
                            inner join db_bridging.V_BatchSetupTickets b on a.Index_Load=b.index_load
                            inner join db_bridging.Batching_plant c on b.BP_ID=c.id_bp
                            inner join db_bridging.Region d on c.id_region=d.id_region
                            WHERE a.`Method`='POST' and c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")
                        ) 
                        UNION ALL                        
                        (   SELECT 'commandbatch' as db, a.`type`, a.`JO_Number`, a.`Jobmix_ERP`, a.`Jobmix_BP`, a.`Load_Size` as `Vol`, a.`RecordDate`, a.`Running_Date`, a.`Index_Load`, a.`Keterangan`, 'gagal' as `status`, a.`Index_Post_Gagal` as `Index_Log`
                            FROM db_bridging.API_Post_Gagal a
                            inner join db_bridging.V_BatchSetupTickets b on a.Index_Load=b.index_load
                            inner join db_bridging.Batching_plant c on b.BP_ID=c.id_bp
                            inner join db_bridging.Region d on c.id_region=d.id_region
                            where c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")
                        ) 
                        UNION ALL                        
                        (   SELECT 'autobatch' as db, a.`type`, a.`JO_Number`, a.`Jobmix_ERP`, a.`Jobmix_BP`, a.`Post_Qty` as `Vol`, a.`CreateDateBP` as `RecordDate`, a.`Method_Date` as `Running_Date`, a.`Index_Load`, a.`Keterangan`, 'sukses' as `status`, `Index_Log` 
                            FROM db_autobatch.API_Logs_Detail a 
                            inner join db_autobatch.TICKET b on a.Index_Load=b.Ticket_Id
                            inner join db_autobatch.Batching_plant c on b.BP_ID=c.id_bp
                            inner join db_autobatch.Region d on c.id_region=d.id_region
                            WHERE a.`Method`='POST' and c.id_region in(" . implode(', ', $this->session->userdata('region_access')) . ")
                        ) 
                        UNION ALL                        
                        (   SELECT 'autobatch' as db, a.`type`, a.`JO_Number`, a.`Jobmix_ERP`, a.`Jobmix_BP`, a.`Load_Size` as `Vol`, a.`RecordDate`, a.`Running_Date`, a.`Index_Load`, a.`Keterangan`, 'gagal' as `status`, a.`Index_Post_Gagal` as `Index_Log`
                            FROM db_autobatch.API_Post_Gagal a
                            inner join db_autobatch.TICKET b on a.Index_Load=b.Ticket_Id
                            inner join db_autobatch.Batching_plant c on b.BP_ID=c.id_bp
                            inner join db_autobatch.Region d on c.id_region=d.id_region
                            where c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")
                        ) 
                    ) z
                    WHERE 1 $Running_Date $search $order $length $offset";
        $result = $this->db->query($query)->result();
        return $result;
    }

    public function totalRecord($start = '', $end = '', $search = '')
    {
        // record date
        if ($start == '' && $end == '') {
            $Running_Date = " and Running_Date BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() + INTERVAL 1 DAY ";
        } else {
            if ($start == '') {
                $start = "2018-01-01 00:00";
            }
            if ($end == '') {
                $end = "CURDATE() + INTERVAL 1 DAY";
            } else {
                $end = "'$end'";
            }

            $Running_Date = " and Running_Date BETWEEN '" . $start . "' AND " . $end . " ";
        }
        // record date


        $search = ($search != '') ? " and CONCAT_WS(' ', JO_Number, Jobmix_ERP, Jobmix_BP, Vol, RecordDate, Running_Date, Index_Load, Keterangan, status) like '%$search%' " : '';

        // $query = "  select COUNT(*) as total FROM(
        //                 (SELECT `type`, `JO_Number`, `Jobmix_ERP`, `Jobmix_BP`, `Post_Qty` as `Vol`, `CreateDateBP` as `RecordDate`, `Method_Date` as `Running_Date`, `Index_Load`, `Keterangan`, 'sukses' as `status` 
        //                 FROM db_bridging.API_Logs_Detail WHERE `Method`='POST') 
        //                 UNION ALL
        //                 (SELECT `type`, `JO_Number`, `Jobmix_ERP`, `Jobmix_BP`, `Load_Size` as `Vol`, `RecordDate`, `Running_Date`, `Index_Load`, `Keterangan`, 'gagal' as `status` 
        //                 FROM db_bridging.API_Post_Gagal) 
        //                 UNION ALL
        //                 (SELECT `type`, `JO_Number`, `Jobmix_ERP`, `Jobmix_BP`, `Post_Qty` as `Vol`, `CreateDateBP` as `RecordDate`, `Method_Date` as `Running_Date`, `Index_Load`, `Keterangan`, 'sukses' as `status` 
        //                 FROM db_autobatch.API_Logs_Detail WHERE `Method`='POST') 
        //                 UNION ALL
        //                 (SELECT `type`, `JO_Number`, `Jobmix_ERP`, `Jobmix_BP`, `Load_Size` as `Vol`, `RecordDate`, `Running_Date`, `Index_Load`, `Keterangan`, 'gagal' as `status` 
        //                 FROM db_autobatch.API_Post_Gagal) 
        //             ) z where 1 $Running_Date $search ";
        $query = "  select COUNT(*) as total FROM(
                        (   SELECT 'commandbatch' as db, a.`type`, a.`JO_Number`, a.`Jobmix_ERP`, a.`Jobmix_BP`, a.`Post_Qty` as `Vol`, a.`CreateDateBP` as `RecordDate`, a.`Method_Date` as `Running_Date`, a.`Index_Load`, a.`Keterangan`, 'sukses' as `status`, `Index_Log` 
                            FROM db_bridging.API_Logs_Detail a 
                            inner join db_bridging.V_BatchSetupTickets b on a.Index_Load=b.index_load
                            inner join db_bridging.Batching_plant c on b.BP_ID=c.id_bp
                            inner join db_bridging.Region d on c.id_region=d.id_region
                            WHERE a.`Method`='POST' and c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")
                        ) 
                        UNION ALL                        
                        (   SELECT 'commandbatch' as db, a.`type`, a.`JO_Number`, a.`Jobmix_ERP`, a.`Jobmix_BP`, a.`Load_Size` as `Vol`, a.`RecordDate`, a.`Running_Date`, a.`Index_Load`, a.`Keterangan`, 'gagal' as `status`, a.`Index_Post_Gagal` as `Index_Log`
                            FROM db_bridging.API_Post_Gagal a
                            inner join db_bridging.V_BatchSetupTickets b on a.Index_Load=b.index_load
                            inner join db_bridging.Batching_plant c on b.BP_ID=c.id_bp
                            inner join db_bridging.Region d on c.id_region=d.id_region
                            where c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")
                        ) 
                        UNION ALL                        
                        (   SELECT 'autobatch' as db, a.`type`, a.`JO_Number`, a.`Jobmix_ERP`, a.`Jobmix_BP`, a.`Post_Qty` as `Vol`, a.`CreateDateBP` as `RecordDate`, a.`Method_Date` as `Running_Date`, a.`Index_Load`, a.`Keterangan`, 'sukses' as `status`, `Index_Log` 
                            FROM db_autobatch.API_Logs_Detail a 
                            inner join db_autobatch.TICKET b on a.Index_Load=b.Ticket_Id
                            inner join db_autobatch.Batching_plant c on b.BP_ID=c.id_bp
                            inner join db_autobatch.Region d on c.id_region=d.id_region
                            WHERE a.`Method`='POST' and c.id_region in(" . implode(', ', $this->session->userdata('region_access')) . ")
                        ) 
                        UNION ALL                        
                        (   SELECT 'autobatch' as db, a.`type`, a.`JO_Number`, a.`Jobmix_ERP`, a.`Jobmix_BP`, a.`Load_Size` as `Vol`, a.`RecordDate`, a.`Running_Date`, a.`Index_Load`, a.`Keterangan`, 'gagal' as `status`, a.`Index_Post_Gagal` as `Index_Log`
                            FROM db_autobatch.API_Post_Gagal a
                            inner join db_autobatch.TICKET b on a.Index_Load=b.Ticket_Id
                            inner join db_autobatch.Batching_plant c on b.BP_ID=c.id_bp
                            inner join db_autobatch.Region d on c.id_region=d.id_region
                            where c.id_region in (" . implode(', ', $this->session->userdata('region_access')) . ")
                        )  
                    ) z where 1 $Running_Date $search ";

        $result = $this->db->query($query)->result();
        return $result[0]->total;
    }
}
