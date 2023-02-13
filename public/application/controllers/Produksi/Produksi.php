<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Produksi extends CI_Controller
{
    var $id_region, $start, $end = '';

    public function index()
    {

        $this->region();
    }

    public function region()
    {
        $id_region = $this->uri->segment(3);
        $mesin = $this->uri->segment(4) == '' ? 'commandbatch' : $this->uri->segment(4);
        $BP_ID = $this->input->get('BP_ID');

        $region = $this->db->get_where('Region', array('id_region' => $id_region))->result();
        $region_name = $region[0]->region_name;

        $select_bp = '<option value="">All BP</option>';
        $bp_options = $this->Model_region->getBpByRegion($id_region, $mesin);

        foreach ($bp_options as $bp_option) {
            $selected = $BP_ID == $bp_option->id_bp ? 'selected' : '';
            $select_bp .= '<option name="BP_ID" id="BP_ID" value="' . $bp_option->id_bp . '" ' . $selected . '>' . $bp_option->bp_name . '</option>';
        }

        // TABS
        $tab_commandbatch = $this->db->get_where('Batching_plant', array('id_region' => $id_region))->result();
        $tab_commandbatch = !empty($tab_commandbatch) ? '<li class="nav-item"> <a class="nav-link ' . ($mesin == 'commandbatch' ? 'active' : '') . '" href="' . ($mesin == 'commandbatch' ? '#' : base_url('produksi/region/' . $id_region . '/commandbatch')) . '">Commandbatch</a> </li>' : '';
        $autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
        $tab_autobatch = $autobatch->get_where('Batching_plant', array('id_region' => $id_region))->result();
        $tab_autobatch = !empty($tab_autobatch) ? '<li class="nav-item"> <a class="nav-link ' . ($mesin == 'autobatch' ? 'active' : '') . '" href="' . ($mesin == 'autobatch' ? '#' : base_url('produksi/region/' . $id_region . '/autobatch')) . '">Autobatch</a> </li>' : '';
        $eurotech = $this->load->database('eurotech', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
        $tab_eurotech = $eurotech->get_where('Batching_plant', array('id_region' => $id_region))->result();
        $tab_eurotech = !empty($tab_eurotech) ? '<li class="nav-item"> <a class="nav-link ' . ($mesin == 'eurotech' ? 'active' : '') . '" href="' . ($mesin == 'eurotech' ? '#' : base_url('produksi/region/' . $id_region . '/eurotech')) . '">eurotech</a> </li>' : '';
        $tabs = !empty($tab_commandbatch) || !empty($tab_autobatch) || !empty($tab_eurotech) ? '<ul class="nav nav-tabs">' . $tab_commandbatch . $tab_autobatch . $tab_eurotech . '</ul>' : '';

        $data = [
            'judul'         => $region_name,
            'user'          => $this->Model_auth->dataLogin(),
            // 'view'          => $this->Model_produksi->viewRegion($id_region, $start, $end),
            'id_region'     => $id_region,
            'tabs'          => $tabs,
            'mesin'         => $mesin,
            'select_bp'     => $select_bp
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('produksi/v_produksi', $data);

        echo "<script> var id_region = '" . $id_region . "'; </script>";
        echo "<script> var mesin = '" . $mesin . "'; </script>";
        $this->load->view('templates/footer');
    }

    public function totalRecord($filter = false)
    {
        $id_region = $this->uri->segment(3);
        $BP_ID = $this->input->get('BP_ID');
        $jo = $this->input->get('jo');
        $sklp = $this->input->get('sklp');
        $mesin = $this->input->get('mesin');
        $tglStart = $this->input->get('tglStart');
        $tglEnd = $this->input->get('tglEnd');

        if ($filter) {
            $search = $this->input->get('search');
            $search =  $search['value'];
        } else {
            $search = '';
        }
        return $this->Model_produksi->totalRecord($id_region, $tglStart, $tglEnd, $search, $BP_ID, $jo, $sklp, $mesin);
    }

    public function ajaxRegion()
    {
        $id_region = $this->uri->segment(3);
        $BP_ID = $this->input->get('BP_ID');
        $jo = $this->input->get('jo');
        $sklp = $this->input->get('sklp');
        // var_dump($jo);
        $mesin = $this->input->get('mesin');
        $tglStart = $this->input->get('tglStart');
        $tglEnd = $this->input->get('tglEnd');
        $length = $this->input->get('length');
        $offset = $this->input->get('start');

        $search =  $this->input->get('search');
        $search =  $search['value'];


        // $column_ref = ['tanggal', 'max_ticket', 'Delivery_Instruction', 'Item_Code', 'Ordered_Qty', 'Customer_Description', 'tanggal', 'total_load', 'total_batch', 'Delivered_Qty', 'bp_name', 'tanggal'];
        if ($mesin == 'commandbatch') {
            $column_ref = ['tanggal', 'max_ticket', 'index_load', 'Delivery_Instruction', 'sklp', 'Item_Code', 'Load_Size', 'Customer_Description', 'tanggal', 'total_load', 'total_batch', 'bp_name'];
        } else if ($mesin == 'autobatch') {
            $column_ref = ['tanggal', 'max_ticket', 'index_load', 'Delivery_Instruction', 'sklp', 'Item_Code', 'Load_Size', 'Customer_Description', 'tanggal', 'total_load', 'total_batch', 'bp_name'];
        } else if ($mesin == 'eurotech') {
            $column_ref = ['tanggal', 'max_ticket', '`index`', 'Delivery_Instruction', 'sklp', 'Item_Code', 'Load_Size', 'Customer_Description', 'tanggal', 'total_load', 'total_batch', 'bp_name'];
        } else {
            $column_ref = ['', '', '', '', '', '', '', '', '', '', ''];
        }

        $order = $this->input->get('order');
        $order = $order[0];
        $order['column'] = $column_ref[$order['column']];


        $data = $this->Model_produksi->viewRegion($id_region, $tglStart, $tglEnd, $length, $offset, $search, $order, $BP_ID, $jo, $sklp, $mesin);

        $dataAjax = array();
        $no = 0;
        foreach ($data as $v) {

            $dataAjax[$no][] = $no + 1 + $offset;
            $dataAjax[$no][] = $v->max_ticket;
            // $dataAjax[$no][] = $mesin == 'commandbatch' ? $v->index_load : '';

            switch ($mesin) {
                case 'commandbatch':
                    $dataAjax[$no][] = $v->index_load;
                    break;
                case 'autobatch':
                    $dataAjax[$no][] = $v->index_load;
                    break;
                case 'eurotech':
                    $dataAjax[$no][] = $v->index;
                    break;

                default:
                    $dataAjax[$no][] = '';
                    break;
            }

            $dataAjax[$no][] = $v->Delivery_Instruction;
            $dataAjax[$no][] = is_null($v->sklp) ? '' : $v->sklp;
            $dataAjax[$no][] = $v->Item_Code . ($mesin == 'commandbatch' ? '<br>' . $v->Other_Code . '<br>' . $v->Consistence : '');
            // $dataAjax[$no][] = round((float) $v->Ordered_Qty, 2);
            $dataAjax[$no][] = round((float) $v->Load_Size, 2);
            $dataAjax[$no][] = $v->Customer_Description;
            $dataAjax[$no][] = date_format(date_create($v->tanggal), 'Y-m-d H:i');

            $formLoad = '<form action="' . base_url('produksi/detail_load/') . $id_region . '/' . $mesin . '" method="get" target="_blank">';

            if (!empty($tglStart)) {
                $formLoad .= '<input type="hidden" name="start" value="' . $tglStart . '">';
            }
            if (!empty($tglEnd)) {
                $formLoad .= '<input type="hidden" name="end" value="' . $tglEnd . '">';
            }


            if ($mesin == 'commandbatch') {
                $formLoad .= '<input type="hidden" name="LoadID" value="' . $v->LoadID . '">';
            } else if ($mesin == 'autobatch') {
                $formLoad .= '<input type="hidden" name="Ticket_Id" value="' . $v->max_ticket . '">';
            } else if ($mesin == 'eurotech') {
                $formLoad .= '<input type="hidden" name="sheet_no" value="' . $v->sheet_no . '">';
            }



            $formLoad .= '<input type="hidden" name="BP_ID" value="' . $v->BP_ID . '">';

            $formLoad .= '<button type="submit" class="btn btn-info badge">
                                        ' . $v->total_load . '
                                    </button>
                        </form>';
            $dataAjax[$no][] = $formLoad;

            $formBatch = '<form action="' . base_url('produksi/detail_batch/') . $id_region . '/' . $mesin . '" method="get" target="_blank">';


            if (!empty($tglStart)) {
                $formBatch .= '<input type="hidden" name="start" value="' . $tglStart . '">';
            }
            if (!empty($tglEnd)) {
                $formBatch .= '<input type="hidden" name="end" value="' . $tglEnd . '">';
            }


            if ($mesin == 'commandbatch') {
                $formBatch .= '<input type="hidden" name="LoadID" value="' . $v->LoadID . '">';
            } else if ($mesin == 'autobatch') {
                $formBatch .= '<input type="hidden" name="LoadID" value="' . $v->max_ticket . '">';
            }

            $formBatch .= '<input type="hidden" name="BP_ID" value="' . $v->BP_ID . '">';


            $formBatch .= '<button type="submit" class="btn btn-info badge">
                                        ' . $v->total_batch . '
                                    </button>
                        </form>';
            $dataAjax[$no][] = $formBatch;

            // $dataAjax[$no][] = round($v->Delivered_Qty, 2) . ' m3';

            $dataAjax[$no][] = $v->bp_name;

            $status = '';
            // if (!is_null($v->OrderID) && $v->OrderID != '') {
            //     $status .= 'ID - ' . $v->Ticket_Status;
            // } else if (!is_null($v->Delivery_Instruction) && $v->Delivery_Instruction != '') {
            //     $status .= 'JO - ' . $v->Ticket_Status;
            // } else {
            //     $status .= 'JMF - ' . $v->Ticket_Status;
            // }

            if ($mesin == 'commandbatch') {
                $this_db = $this->db;
            } else if ($mesin == 'autobatch') {
                $this_db = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
            }

            if ($mesin == 'eurotech') {
                $status .= 'Eurotech not<br>ready for<br>this feature.';
            } else if (empty($v->Delivery_Instruction)) {
                // JO Kosong
                $status .= "JO Kosong";
            } else if (!empty($v->api_sukses)) {
                // JO Sudah Dipost
                $status .= '<div class="badge badge-success">Posted</div>';
            } else if (!empty($v->api_gagal)) {
                // JO Gagal Dipost
                $status .= '<a target="_blank" href="' . base_url('Api/repost') . '?type=' . $v->type . '&Index_Post_Gagal=' . $v->api_gagal . '&mesin=' . $mesin . '"><div class="badge badge-danger">Post Gagal</div></a>';
            } else if (!empty($v->api_sukses_last_id)) {
                // JO Valid, pernah dipost load lain
                if ($v->index_load < $v->api_sukses_last_id) {
                    // JO ke skip crontab
                    $jo_exist = $this_db->query("select JO_Number from API_Logs_Header where JO_Number='$v->Delivery_Instruction'")->result();
                    if (empty($jo_exist)) {
                        // JO Sudah Tak Aktif di API
                        $status .= 'JO Number<br>Sudah Tidak<br>Tersedia di<br>API Active.<br>';
                    } else {
                        // Post Manual
                        $status .= '<a target="_blank" href="' . base_url('Api/post') . '?type=' . $v->type . '&Index_Load=' . $v->index_load . '&mesin=' . $mesin . '"><div class="badge badge-warning text-white">Post Manual</div></a>';
                    }
                } else {
                    // JO Belum dieksekusi crontab
                    $jo_exist = $this_db->query("select JO_Number from API_Logs_Header where JO_Number='$v->Delivery_Instruction'")->result();
                    if (empty($jo_exist)) {
                        // JO Sudah Tak Aktif di API
                        $status .= 'JO Number<br>Sudah Tidak<br>Tersedia di<br>API Active.<br>';
                    } else {
                        // JO Nunggu Giliran
                        $status .= 'Api<br>Waiting';
                    }
                }
            } else {
                // JO Perlu dicek di API, belum pernah dipost load lain
                $jo_exist = $this_db->query("select JO_Number from API_Logs_Header where JO_Number='$v->Delivery_Instruction'")->result();
                if (empty($jo_exist)) {
                    // JO Tidak Ditemukan di API
                    $status .= 'JO Number<br>Not Found<br>in<br>API Active.<br>';
                } else {
                    // JO Valid, Nunggu Giliran
                    $status .= 'Api<br>Waiting';
                }
            }

            $dataAjax[$no][] = $status;

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



    // public function detail_load()
    // {
    //     $id_region = $this->uri->segment(3);
    //     $mesin = $this->uri->segment(4);
    //     $start = $this->input->get('start');
    //     $end = $this->input->get('end');
    //     $OrderID = $this->input->get('OrderID');
    //     $Delivery_Instruction = $this->input->get('Delivery_Instruction');
    //     $Item_Code = $this->input->get('Item_Code');
    //     $BP_ID = $this->input->get('BP_ID');

    //     $data = $this->Model_produksi->detailLoadProduksi($id_region, $OrderID, $Delivery_Instruction, $Item_Code, $start, $end, $BP_ID, $mesin);
    //     foreach ($data as $val) {
    //         if ($mesin == 'commandbatch') {
    //             $load_lines = $this->Model_produksi->detailLoadMaterial($val->LoadID, $val->BP_ID, $mesin);
    //             $val->Load_Lines = $load_lines;
    //         } else if ($mesin == 'autobatch') {
    //             $load_lines = $this->Model_produksi->detailLoadMaterial($val->Ticket_Code, $val->BP_ID, $mesin);
    //             $val->Load_Lines = $load_lines;
    //         }
    //     }


    //     $data = [
    //         'judul'         => 'Detail load',
    //         'user'          => $this->Model_auth->dataLogin(),
    //         'data' => $data,
    //         'id_region'     => $id_region

    //     ];

    //     $this->load->view('templates/header', $data);
    //     $this->load->view('templates/topbar');
    //     $this->load->view('templates/sidebar', $data);
    //     $this->load->view('produksi/v_detail_load', $data);
    //     $this->load->view('templates/footer');
    // }

    // public function detail_batch()
    // {
    //     $id_region = $this->uri->segment(3);
    //     $start = $this->input->get('start');
    //     $end = $this->input->get('end');
    //     $OrderID = $this->input->get('OrderID');
    //     $Delivery_Instruction = $this->input->get('Delivery_Instruction');
    //     $Item_Code = $this->input->get('Item_Code');
    //     $BP_ID = $this->input->get('BP_ID');

    //     $data = [
    //         'judul'         => 'Detail batch',
    //         'user'          => $this->Model_auth->dataLogin(),
    //         'data'          => $this->Model_produksi->detailbathProduksi($id_region, $OrderID, $Delivery_Instruction, $Item_Code, $start, $end, $BP_ID),
    //         'id_region'     => $this->id_region,
    //     ];


    //     $this->load->view('templates/header', $data);
    //     $this->load->view('templates/topbar');
    //     $this->load->view('templates/sidebar', $data);
    //     $this->load->view('produksi/detail_batch', $data);
    //     $this->load->view('templates/footer');
    // }
}
