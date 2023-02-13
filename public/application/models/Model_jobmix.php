<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_jobmix extends CI_Model
{
    public function index($id = null, $length = '', $offset = '', $search = '', $order = '', $mesin = 'commandbatch')
    {
        if ($mesin == 'commandbatch') {
            return $this->index_commandbatch($id, $length, $offset, $search, $order);
        } elseif ($mesin == 'autobatch') {
            return $this->index_autobatch($id, $length, $offset, $search, $order);
        } else {
            return array();
        }
    }

    public function index_commandbatch($id = null, $length = '', $offset = '', $search = '', $order = '')
    {

        $id = is_null($id) ? "" : "and c.id_region=" . $id;


        $length = $length != '' ? ' limit ' . $length : '';
        $offset = $offset != '' ? ' offset ' . $offset : '';

        $search = ($search != '' && empty($having)) ? " and CONCAT_WS(' ', a.jobmix_code, b.jumlah, a.UpdatedBy, c.bp_name, d.region_name) like '%$search%' " : '';

        // $BP_ID = $BP_ID != '' ? " and a.BP_ID=$BP_ID " : '';

        // order $order['column']-$order['dir']
        $order     = $order  != '' ? ' order by ' . $order['column'] . ' ' . $order['dir'] . ' ' : " order by lastupdate desc ";


        $query = "  SELECT a.jobmix_code, b.jumlah, max(a.RecordDate) as lastupdate, a.UpdatedBy, a.BP_ID, c.bp_name, d.region_name
                    FROM `MixDesign` a
                    INNER join (SELECT jobmix_code, count(DISTINCT RecordDate) as jumlah FROM `MixDesign` WHERE 1 GROUP by jobmix_code, BP_ID ORDER by jobmix_code) as b on a.jobmix_code = b.jobmix_code
                    inner join Batching_plant c on a.BP_ID = c.id_bp
                    inner join Region d on d.id_region = c.id_region
                    WHERE 1 $id $search group by a.jobmix_code, a.BP_ID $order $length $offset ";
        return $this->db->query($query)->result();
    }
    public function index_autobatch($id = null, $length = '', $offset = '', $search = '', $order = '')
    {
        $autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
        $id = is_null($id) ? "" : "and c.id_region=" . $id;


        $length = $length != '' ? ' limit ' . $length : '';
        $offset = $offset != '' ? ' offset ' . $offset : '';

        $search = ($search != '' && empty($having)) ? " and CONCAT_WS(' ', a.Jobmix_Code, b.jumlah, a.Updatedby, c.bp_name, d.region_name) like '%$search%' " : '';

        // $BP_ID = $BP_ID != '' ? " and a.BP_ID=$BP_ID " : '';

        // order $order['column']-$order['dir']
        $order     = $order  != '' ? ' order by ' . $order['column'] . ' ' . $order['dir'] . ' ' : " order by lastupdate desc ";


        $query = "  SELECT a.Jobmix_Code jobmix_code, b.jumlah, CASE WHEN a.Updateddate IS NOT NULL AND a.Updateddate != '' THEN a.Updateddate ELSE a.Createddate END lastupdate, 
                        CASE WHEN a.Updatedby IS NOT NULL AND a.Updatedby !='' THEN a.Updatedby ELSE a.Createdby END UpdatedBy, a.BP_ID, c.bp_name, d.region_name
                    FROM `JOBMIX_HEADER` a
                    inner join (select Jobmix_Code, count(Jobmix_Code) jumlah from JOBMIX_HEADER where 1 group by Jobmix_Code) b on a.Jobmix_Code = b.Jobmix_Code
                    inner join Batching_plant c on a.BP_ID = c.id_bp
                    inner join Region d on d.id_region = c.id_region
                    WHERE 1 $id $search group by a.Jobmix_Code, a.BP_ID $order $length $offset ";
        return $autobatch->query($query)->result();
    }

    public function totalRecord($id, $search = '', $mesin = 'commandbatch')
    {
        if ($mesin == 'commandbatch') {
            return $this->totalRecord_commanbatch($id, $search);
        } elseif ($mesin == 'autobatch') {
            return $this->totalRecord_autobatch($id, $search);
        } else {
            return array();
        }
    }

    public function totalRecord_commanbatch($id, $search = '')
    {
        $id = is_null($id) ? "" : "and c.id_region=" . $id;
        $search = ($search != '') ? " and CONCAT_WS(' ', a.jobmix_code, b.jumlah, a.UpdatedBy, c.bp_name, d.region_name) like '%$search%' " : '';

        // $BP_ID = $BP_ID != '' ? " and a.BP_ID=$BP_ID " : '';

        $query = "  select COUNT(*) as total FROM(
                        SELECT a.jobmix_code, b.jumlah, max(a.RecordDate) as lastupdate, a.UpdatedBy, a.BP_ID, c.bp_name, d.region_name
                        FROM `MixDesign` a
                        INNER join (SELECT jobmix_code, count(DISTINCT RecordDate) as jumlah FROM `MixDesign` WHERE 1 GROUP by jobmix_code, BP_ID ORDER by jobmix_code) as b on a.jobmix_code = b.jobmix_code
                        inner join Batching_plant c on a.BP_id = c.id_bp
                        inner join Region d on d.id_region = c.id_region
                        WHERE 1 $id $search group by a.jobmix_code, a.BP_ID 
                    ) x where 1";

        $result = $this->db->query($query)->result();
        return $result[0]->total;
    }

    public function totalRecord_autobatch($id, $search = '')
    {
        $autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
        $id = is_null($id) ? "" : "and c.id_region=" . $id;
        $search = ($search != '') ? " and CONCAT_WS(' ', a.jobmix_code, b.jumlah, a.UpdatedBy, c.bp_name, d.region_name) like '%$search%' " : '';

        // $BP_ID = $BP_ID != '' ? " and a.BP_ID=$BP_ID " : '';

        $query = "  select COUNT(*) as total FROM(
                        SELECT a.Jobmix_Code jobmix_code, b.jumlah, CASE WHEN a.Updateddate IS NOT NULL AND a.Updateddate != '' THEN a.Updateddate ELSE a.Createddate END lastupdate, 
                            CASE WHEN a.Updatedby IS NOT NULL AND a.Updatedby !='' THEN a.Updatedby ELSE a.Createdby END UpdatedBy, a.BP_ID, c.bp_name, d.region_name
                        FROM `JOBMIX_HEADER` a
                        inner join (select Jobmix_Code, count(Jobmix_Code) jumlah from JOBMIX_HEADER where 1 group by Jobmix_Code) b on a.Jobmix_Code = b.Jobmix_Code
                        inner join Batching_plant c on a.BP_ID = c.id_bp
                        inner join Region d on d.id_region = c.id_region
                        WHERE 1 $id $search group by a.Jobmix_Code, a.BP_ID
                    ) x where 1";

        $result = $autobatch->query($query)->result();
        return $result[0]->total;
    }


    public function detail_jobmix($jobmix_code, $BP_ID, $max_date = '', $limit = '', $mesin = 'commandbatch')
    {
        if ($mesin == 'commandbatch') {
            return $this->detail_jobmix_commandbatch($jobmix_code, $BP_ID, $max_date, $limit);
        } elseif ($mesin == 'autobatch') {
            return $this->detail_jobmix_autobatch($jobmix_code, $BP_ID, $max_date, $limit);
        } else {
            return array();
        }
    }

    public function detail_jobmix_commandbatch($jobmix_code, $BP_ID, $max_date = '', $limit = '')
    {
        $max_date = $max_date == '' ? '' : " and a.RecordDate < '$max_date'";
        $limit = $limit == '' ? '' : " limit $limit ";

        $query = "SELECT group_concat(concat_ws('|', a.material_code, a.material_desc, a.entry_qty, a.entry_uom) separator '<br>') bahan, a.volume, a.RecordDate, a.UpdatedBy
				FROM `MixDesign` a
				inner join Batching_plant b on a.BP_ID=b.id_bp
				WHERE a.jobmix_code = " . $this->db->escape($jobmix_code) . " and a.BP_ID = " . $BP_ID . " $max_date group by a.RecordDate order by a.RecordDate desc $limit";
        return $this->db->query($query)->result();
    }
    public function detail_jobmix_autobatch($jobmix_code, $BP_ID, $max_date = '', $limit = '')
    {
        $autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
        $query = "SELECT group_concat(concat_ws('|', b.Material_Code, b.Material_Name, b.Qty_Material, b.Material_Uom) separator '<br>') bahan,
                a.Qty_Jobmix volume, CASE WHEN a.Updateddate IS NOT NULL AND a.Updateddate != '' THEN a.Updateddate ELSE a.Createddate END RecordDate, CASE WHEN a.Updatedby IS NOT NULL AND a.Updatedby !='' THEN a.Updatedby ELSE a.Createdby END UpdatedBy
				FROM `JOBMIX_HEADER` a
				inner join JOBMIX_DETAIL b on a.Jobmix_Id=b.Jobmix_Id
				WHERE a.Jobmix_Code = '" . $jobmix_code . "' and b.BP_ID = " . $BP_ID . " group by a.Jobmix_Id order by RecordDate desc ";
        return $autobatch->query($query)->result();
    }
}
