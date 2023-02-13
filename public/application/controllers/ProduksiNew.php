<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProduksiNew extends CI_Controller
{
    var $id_region, $start, $end = '';

    public function index()
    {

        $this->region();
    }

    public function region()
    {
        $id_region = $this->uri->segment(3);
        $BP_ID = $this->input->get('BP_ID');
        // $start = $this->input->get('start');
        // $end = $this->input->get('end');

        $region = $this->db->get('Region')->result();

        foreach ($region as $row) {
            if ($row->id_region === $id_region) {
                $j = $row->region_name;
            }
        }


        $select_bp = '<option value="">All</option>';
        $bp_options = $this->Model_region->getBpByRegion($id_region);

        foreach ($bp_options as $bp_option) {
            $selected = $BP_ID == $bp_option->id_bp ? 'selected' : '';
            $select_bp .= '<option name="BP_ID" id="BP_ID" value="' . $bp_option->id_bp . '" ' . $selected . '>' . $bp_option->bp_name . '</option>';
        }

        $data = [
            'judul'         => $j,
            'user'          => $this->Model_auth->dataLogin(),
            // 'view'          => $this->Model_produksi->viewRegion($id_region, $start, $end),
            'id_region'     => $id_region,
            'select_bp'     => $select_bp
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('produksi/index', $data);
        echo "<script> var id_region = '" . $this->uri->segment(3) . "'; </script>";
        $this->load->view('templates/footer');
    }

    public function detail_load()
    {
        $id_region = $this->uri->segment(3);
        $start = $this->input->get('start');
        $end = $this->input->get('end');
        $OrderID = $this->input->get('OrderID');
        $Delivery_Instruction = $this->input->get('Delivery_Instruction');
        $Item_Code = $this->input->get('Item_Code');
        $BP_ID = $this->input->get('BP_ID');

        $data = $this->Model_produksi->detailLoadProduksi($id_region, $OrderID, $Delivery_Instruction, $Item_Code, $start, $end, $BP_ID);
        foreach ($data as $val) {
            $load_lines = $this->Model_produksi->detailLoadMaterial($val->LoadID, $val->BP_ID);
            $val->Load_Lines = $load_lines;
        }


        $data = [
            'judul'         => 'Detail load',
            'user'          => $this->Model_auth->dataLogin(),
            'data' => $data,
            'id_region'     => $id_region

        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('produksi/detail_load', $data);
        $this->load->view('templates/footer');
    }

    public function detail_batch()
    {
        $id_region = $this->uri->segment(3);
        $start = $this->input->get('start');
        $end = $this->input->get('end');
        $OrderID = $this->input->get('OrderID');
        $Delivery_Instruction = $this->input->get('Delivery_Instruction');
        $Item_Code = $this->input->get('Item_Code');
        $BP_ID = $this->input->get('BP_ID');

        $data = [
            'judul'         => 'Detail batch',
            'user'          => $this->Model_auth->dataLogin(),
            'data'          => $this->Model_produksi->detailbathProduksi($id_region, $OrderID, $Delivery_Instruction, $Item_Code, $start, $end, $BP_ID),
            'id_region'     => $this->id_region,
        ];


        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('produksi/detail_batch', $data);
        $this->load->view('templates/footer');
    }


    public function totalRecord($filter = false)
    {
        $id_region = $this->uri->segment(3);
        $BP_ID = $this->input->get('BP_ID');
        $tglStart = $this->input->get('tglStart');
        $tglEnd = $this->input->get('tglEnd');

        if ($filter) {
            $search = $this->input->get('search');
            $search =  $search['value'];
        } else {
            $search = '';
        }
        return $this->Model_produksi->totalRecord($id_region, $tglStart, $tglEnd, $search, $BP_ID);
    }

    public function ajaxRegion()
    {
        $id_region = $this->uri->segment(3);
        $BP_ID = $this->input->get('BP_ID');
        $tglStart = $this->input->get('tglStart');
        $tglEnd = $this->input->get('tglEnd');
        $length = $this->input->get('length');
        $offset = $this->input->get('start');

        $search =  $this->input->get('search');
        $search =  $search['value'];


        $column_ref = ['tanggal', 'max_ticket', 'Delivery_Instruction', 'Item_Code', 'Ordered_Qty', 'Customer_Description', 'tanggal', 'total_load', 'total_batch', 'Delivered_Qty', 'bp_name', 'tanggal'];
        $order = $this->input->get('order');
        $order = $order[0];
        $order['column'] = $column_ref[$order['column']];


        $data = $this->Model_produksi->viewRegion($id_region, $tglStart, $tglEnd, $length, $offset, $search, $order, $BP_ID);

        $dataAjax = array();
        $no = 0;
        foreach ($data as $v) {

            $dataAjax[$no][] = $no + 1 + $offset;
            $dataAjax[$no][] = $v->max_ticket;
            $dataAjax[$no][] = $v->Delivery_Instruction;
            $dataAjax[$no][] = $v->Item_Code;
            $dataAjax[$no][] = round((float) $v->Ordered_Qty, 2);
            $dataAjax[$no][] = $v->Customer_Description;
            $dataAjax[$no][] = date_format(date_create($v->tanggal), 'Y-m-d H:i');

            $formLoad = '<form action="' . base_url('ProduksiNew/detail_load/') . $id_region . '" method="get" target="_blank">';

            if (!empty($tglStart)) {
                $formLoad .= '<input type="hidden" name="start" value="' . $tglStart . '">';
            }
            if (!empty($tglEnd)) {
                $formLoad .= '<input type="hidden" name="end" value="' . $tglEnd . '">';
            }


            if (!is_null($v->OrderID) && $v->OrderID != '') {
                $formLoad .= '<input type="hidden" name="OrderID" value="' . $v->OrderID . '">';
            } else if (!is_null($v->Delivery_Instruction) && $v->Delivery_Instruction != '') {
                $formLoad .= '<input type="hidden" name="Delivery_Instruction" value="' . $v->Delivery_Instruction . '">';
                $formLoad .= '<input type="hidden" name="Item_Code" value="' . $v->Item_Code . '">';
            } else {
                $formLoad .= '<input type="hidden" name="Item_Code" value="' . $v->Item_Code . '">';
            }


            $formLoad .= '<input type="hidden" name="BP_ID" value="' . $v->BP_ID . '">';

            $formLoad .= '<button type="submit" class="btn btn-info badge">
                                        ' . $v->total_load . '
                                    </button>
                        </form>';
            $dataAjax[$no][] = $formLoad;

            $formBatch = '<form action="' . base_url('ProduksiNew/detail_batch/') . $id_region . '" method="get" target="_blank">';


            if (!empty($tglStart)) {
                $formBatch .= '<input type="hidden" name="start" value="' . $tglStart . '">';
            }
            if (!empty($tglEnd)) {
                $formBatch .= '<input type="hidden" name="end" value="' . $tglEnd . '">';
            }


            if (!is_null($v->OrderID) && $v->OrderID != '') {
                $formBatch .= '<input type="hidden" name="OrderID" value="' . $v->OrderID . '">';
            } else if (!is_null($v->Delivery_Instruction) && $v->Delivery_Instruction != '') {
                $formBatch .= '<input type="hidden" name="Delivery_Instruction" value="' . $v->Delivery_Instruction . '">';
                $formBatch .= '<input type="hidden" name="Item_Code" value="' . $v->Item_Code . '">';
            } else {
                $formBatch .= '<input type="hidden" name="Item_Code" value="' . $v->Item_Code . '">';
            }

            $formBatch .= '<input type="hidden" name="BP_ID" value="' . $v->BP_ID . '">';


            $formBatch .= '<button type="submit" class="btn btn-info badge">
                                        ' . $v->total_batch . '
                                    </button>
                        </form>';
            $dataAjax[$no][] = $formBatch;

            $dataAjax[$no][] = round($v->Delivered_Qty, 2) . ' m3';

            $dataAjax[$no][] = $v->bp_name;

            $status = '';
            if (!is_null($v->OrderID) && $v->OrderID != '') {
                $status .= 'ID - ' . $v->Ticket_Status;
            } else if (!is_null($v->Delivery_Instruction) && $v->Delivery_Instruction != '') {
                $status .= 'JO - ' . $v->Ticket_Status;
            } else {
                $status .= 'JMF - ' . $v->Ticket_Status;
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
}
