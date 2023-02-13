<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jobmix extends CI_Controller
{


    public function index()
    {
        $this->region();
    }

    public function region()
    {
        $id_region = $this->uri->segment(3);
        $mesin = $this->uri->segment(4) == '' ? 'commandbatch' : $this->uri->segment(4);
        $region_name = $this->Model_region->regionByRegionId($id_region);
        $region_name = empty($region_name) ? "" : $region_name[0]['region_name'];

        // TABS
        $tab_commandbatch = $this->db->get_where('Batching_plant', array('id_region' => $id_region))->result();
        $tab_commandbatch = !empty($tab_commandbatch) ? '<li class="nav-item"> <a class="nav-link ' . ($mesin == 'commandbatch' ? 'active' : '') . '" href="' . ($mesin == 'commandbatch' ? '#' : base_url('Jobmix/region/' . $id_region . '/commandbatch')) . '">Commandbatch</a> </li>' : '';
        $autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
        $tab_autobatch = $autobatch->get_where('Batching_plant', array('id_region' => $id_region))->result();
        $tab_autobatch = !empty($tab_autobatch) ? '<li class="nav-item"> <a class="nav-link ' . ($mesin == 'autobatch' ? 'active' : '') . '" href="' . ($mesin == 'autobatch' ? '#' : base_url('Jobmix/region/' . $id_region . '/autobatch')) . '">Autobatch</a> </li>' : '';
        $tabs = !empty($tab_commandbatch) || !empty($tab_autobatch) ? '<ul class="nav nav-tabs">' . $tab_commandbatch . $tab_autobatch . '</ul>' : '';

        $data = [
            'judul'         => 'Jobmix ' . $region_name,
            'user'          => $this->Model_auth->dataLogin(),
            'mesin'         => $mesin,
            'tabs'          => $tabs,
            // 'data'          => $this->Model_jobmix->index($id_region),
            'region_name'   => $region_name,
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('jobmix/index', $data);
        echo "<script> var id_region = '" . $id_region . "'; </script>";
        echo "<script> var mesin = '" . $mesin . "'; </script>";
        $this->load->view('templates/footer');
    }

    public function detail_jobmix()
    {
        $BP_ID = $this->input->get('BP_ID');
        $mesin = $this->uri->segment(4) == '' ? 'commandbatch' : $this->uri->segment(4);
        $jobmix_code = $this->input->get('jobmix_code');
        $region_bp = $this->Model_region->regionBpByBpId($BP_ID);
        $region_bp = empty($region_bp) ? "" : $region_bp[0]['region_name'] . ' - ' . $region_bp[0]['bp_name'];

        $data = [
            'judul'         => 'Jobmix ' . $jobmix_code,
            'user'          => $this->Model_auth->dataLogin(),
            'data'          => $this->Model_jobmix->detail_jobmix($jobmix_code, $BP_ID, '', '', $mesin),
            'region_bp'     => $region_bp,
            'jobmix_code'   => $jobmix_code
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        echo "<script> var mesin = '" . $mesin . "'; </script>";
        $this->load->view('jobmix/detail_jobmix', $data);
        $this->load->view('templates/footer');
    }

    // AJAX
    public function totalRecord($filter = false)
    {
        $id_region = $this->uri->segment(3);
        $mesin = $this->input->get('mesin');

        if ($filter) {
            $search = $this->input->get('search');
            $search =  $search['value'];
        } else {
            $search = '';
        }
        return $this->Model_jobmix->totalRecord($id_region, $search, $mesin);
    }

    public function ajaxRegion()
    {
        $id_region = $this->uri->segment(3);
        $mesin = $this->input->get('mesin');
        $length = $this->input->get('length');
        $offset = $this->input->get('start');

        $search =  $this->input->get('search');
        $search =  $search['value'];


        $column_ref = ['lastupdate', 'region_name', 'bp_name', 'jobmix_code', 'jumlah', 'lastupdate', 'UpdatedBy', 'lastupdate'];
        $order = $this->input->get('order');
        $order = $order[0];
        $order['column'] = $column_ref[$order['column']];


        $data = $this->Model_jobmix->index($id_region, $length, $offset, $search, $order, $mesin);

        $dataAjax = array();
        $no = 0;
        foreach ($data as $v) {

            $dataAjax[$no][] = $no + 1 + $offset;
            $dataAjax[$no][] = $v->region_name;
            $dataAjax[$no][] = $v->bp_name;
            $dataAjax[$no][] = $v->jobmix_code;
            $dataAjax[$no][] = $v->jumlah;
            $dataAjax[$no][] = date_format(date_create($v->lastupdate), "Y-m-d H:i");
            $dataAjax[$no][] = $v->UpdatedBy;

            $formDetail        = '<form method="get" action="' . base_url("Jobmix/detail_jobmix/$id_region/$mesin") . '" target="_blank">
                                    <input type="hidden" name="jobmix_code" id="jobmix_code" value="' . $v->jobmix_code . '">
                                    <input type="hidden" name="BP_ID" id="BP_ID" value="' . $v->BP_ID . '">
                                    <input type="submit" class="btn btn-sm btn-info" value="detail">
                                </form>';
            $dataAjax[$no][] = $formDetail;

            $no++;
        }
        $totalRecord = $this->totalRecord();
        $totalRecordFiltered = $this->totalRecord(true);
        $response = [
            // "draw" => $offset,
            "recordsFiltered" => $search != '' ? $totalRecordFiltered : $totalRecord,
            "recordsTotal" => $totalRecord,
            "data"  => $dataAjax,
            "error" => '',
        ];


        echo json_encode($response);
    }
}
