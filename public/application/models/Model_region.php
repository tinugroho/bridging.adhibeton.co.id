<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_region extends CI_Model
{
    public function index()
    {
    }

    public function allRegion()
    {
        $this->db->select('*');
        $this->db->from('Region');
        $result = $this->db->get();
        return $result->result_array();
    }
    public function regionByRegionId($id_region)
    {
        $this->db->select('region_name');
        $this->db->from('Region');
        $this->db->where('id_region', $id_region);
        $result = $this->db->get();
        return $result->result_array();
    }
    public function regionByBpId($BP_ID)
    {
        $this->db->select('region_name');
        $this->db->from('Region');
        $this->db->join('Batching_plant', 'Batching_plant.id_region = Region.id_region', 'inner');
        $this->db->where('Batching_plant.id_bp', $BP_ID);
        $result = $this->db->get();
        return $result->result_array();
    }
    public function regionBpByBpId($BP_ID)
    {
        $this->db->select('region_name, bp_name');
        $this->db->from('Region');
        $this->db->join('Batching_plant', 'Batching_plant.id_region = Region.id_region', 'inner');
        $this->db->where('Batching_plant.id_bp', $BP_ID);
        $result = $this->db->get();
        return $result->result_array();
    }
    public function getBpByRegion($id_region, $mesin = 'commandbatch')
    {
        if ($mesin == 'commandbatch') {
            $this->db->select('*');
            $this->db->from('Batching_plant');
            $this->db->where('id_region', $id_region);
            $result = $this->db->get();
            return $result->result();
        } else if ($mesin == 'autobatch') {
            $autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
            $autobatch->select('*');
            $autobatch->from('Batching_plant');
            $autobatch->where('id_region', $id_region);
            $result = $autobatch->get();
            return $result->result();
        } else {
            return array();
        }
    }
}
