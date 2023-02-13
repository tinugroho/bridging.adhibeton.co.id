<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DetailLoad extends CI_Controller
{
    public function detail_load()
    {
        $id_region = $this->uri->segment(3);
        $mesin = $this->uri->segment(4);
        $start = $this->input->get('start');
        $end = $this->input->get('end');
        $LoadID = $this->input->get('LoadID'); //commanbatch
        $Ticket_Id = $this->input->get('Ticket_Id'); //autobatch
        $sheet_no  = $this->input->get('sheet_no'); //eurotech
        $BP_ID = $this->input->get('BP_ID');

        if (isset($_POST['JO_Number'])) {
            $change_all = isset($_POST['change_all']) ? $_POST['change_all'] : '';
            $this->Model_produksi->updateJoNumber($LoadID, $Ticket_Id, $sheet_no, $_POST['JO_Number'], $change_all, $mesin);
        }

        $data = $this->Model_produksi->detailLoadProduksi($id_region, $LoadID, $Ticket_Id, $sheet_no, $start, $end, $BP_ID, $mesin);
        $jobmix = !empty($data) ? $this->Model_jobmix->detail_jobmix(reset($data)->Item_Code, $BP_ID, $end, 3, $mesin) : [];
        // echo "<pre>" . var_export($data, true) . "</pre>";

        $data = [
            'judul'         => 'Detail Load',
            'user'          => $this->Model_auth->dataLogin(),
            'mesin'        => $mesin,
            'jobmix'        => $jobmix,
            'data' => $data,
            'id_region'     => $id_region,

        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        echo "<script> var id_region = '" . $id_region . "'; </script>";
        echo "<script> var mesin = '" . $mesin . "'; </script>";
        $this->load->view('produksi/v_detail_load', $data);
        $this->load->view('templates/footer');
    }

    // public function totalRecord($filter = false)
    // {
    //     $id_region = $this->uri->segment(3);
    //     $BP_ID = $this->input->get('BP_ID');
    //     $mesin = $this->input->get('mesin');
    //     $tglStart = $this->input->get('tglStart');
    //     $tglEnd = $this->input->get('tglEnd');

    //     if ($filter) {
    //         $search = $this->input->get('search');
    //         $search =  $search['value'];
    //     } else {
    //         $search = '';
    //     }
    //     return $this->Model_produksi->totalRecord($id_region, $tglStart, $tglEnd, $search, $BP_ID, $mesin);
    // }

    // public function ajaxDetailLoad()
    // {
    //     $id_region = $this->uri->segment(3);
    //     $BP_ID = $this->input->get('BP_ID');
    //     $mesin = $this->input->get('mesin');
    //     $tglStart = $this->input->get('tglStart');
    //     $tglEnd = $this->input->get('tglEnd');
    //     $length = $this->input->get('length');
    //     $offset = $this->input->get('start');

    //     $search =  $this->input->get('search');
    //     $search =  $search['value'];


    //     $column_ref = ['tanggal', 'max_ticket', 'Delivery_instruction', 'Item_Code', 'Ordered_Qty', 'Customer_Description', 'tanggal', 'total_load', 'total_batch', 'Delivered_Qty', 'bp_name', 'tanggal'];
    //     $order = $this->input->get('order');
    //     $order = $order[0];
    //     $order['column'] = $column_ref[$order['column']];


    //     $data = $this->Model_produksi->viewRegion($id_region, $tglStart, $tglEnd, $length, $offset, $search, $order, $BP_ID, $mesin);

    //     $dataAjax = array();
    //     $no = 0;
    //     foreach ($data as $v) {

    //         $dataAjax[$no][] = $no + 1 + $offset;
    //         $dataAjax[$no][] = $v->max_ticket;
    //         $dataAjax[$no][] = $v->Delivery_instruction;
    //         $dataAjax[$no][] = $v->Item_Code;
    //         $dataAjax[$no][] = round((float) $v->Ordered_Qty, 2);
    //         $dataAjax[$no][] = $v->Customer_Description;
    //         $dataAjax[$no][] = date_format(date_create($v->tanggal), 'Y-m-d H:i');

    //         $formLoad = '<form action="' . base_url('produksi/detail_load/') . $id_region . '/' . $mesin . '" method="get" target="_blank">';

    //         if (!empty($tglStart)) {
    //             $formLoad .= '<input type="hidden" name="start" value="' . $tglStart . '">';
    //         }
    //         if (!empty($tglEnd)) {
    //             $formLoad .= '<input type="hidden" name="end" value="' . $tglEnd . '">';
    //         }


    //         if (!is_null($v->OrderID) && $v->OrderID != '') {
    //             $formLoad .= '<input type="hidden" name="OrderID" value="' . $v->OrderID . '">';
    //         } else if (!is_null($v->Delivery_instruction) && $v->Delivery_instruction != '') {
    //             $formLoad .= '<input type="hidden" name="Delivery_instruction" value="' . $v->Delivery_instruction . '">';
    //             $formLoad .= '<input type="hidden" name="Item_Code" value="' . $v->Item_Code . '">';
    //         } else {
    //             $formLoad .= '<input type="hidden" name="Item_Code" value="' . $v->Item_Code . '">';
    //         }


    //         $formLoad .= '<input type="hidden" name="BP_ID" value="' . $v->BP_ID . '">';

    //         $formLoad .= '<button type="submit" class="btn btn-info badge">
    //                                     ' . $v->total_load . '
    //                                 </button>
    //                     </form>';
    //         $dataAjax[$no][] = $formLoad;

    //         $formBatch = '<form action="' . base_url('produksi/detail_batch/') . $id_region . '/' . $mesin . '" method="get" target="_blank">';


    //         if (!empty($tglStart)) {
    //             $formBatch .= '<input type="hidden" name="start" value="' . $tglStart . '">';
    //         }
    //         if (!empty($tglEnd)) {
    //             $formBatch .= '<input type="hidden" name="end" value="' . $tglEnd . '">';
    //         }


    //         if (!is_null($v->OrderID) && $v->OrderID != '') {
    //             $formBatch .= '<input type="hidden" name="OrderID" value="' . $v->OrderID . '">';
    //         } else if (!is_null($v->Delivery_instruction) && $v->Delivery_instruction != '') {
    //             $formBatch .= '<input type="hidden" name="Delivery_instruction" value="' . $v->Delivery_instruction . '">';
    //             $formBatch .= '<input type="hidden" name="Item_Code" value="' . $v->Item_Code . '">';
    //         } else {
    //             $formBatch .= '<input type="hidden" name="Item_Code" value="' . $v->Item_Code . '">';
    //         }

    //         $formBatch .= '<input type="hidden" name="BP_ID" value="' . $v->BP_ID . '">';


    //         $formBatch .= '<button type="submit" class="btn btn-info badge">
    //                                     ' . $v->total_batch . '
    //                                 </button>
    //                     </form>';
    //         $dataAjax[$no][] = $formBatch;

    //         $dataAjax[$no][] = round($v->Delivered_Qty, 2) . ' m3';

    //         $dataAjax[$no][] = $v->bp_name;

    //         $status = '';
    //         if (!is_null($v->OrderID) && $v->OrderID != '') {
    //             $status .= 'ID - ' . $v->Ticket_Status;
    //         } else if (!is_null($v->Delivery_instruction) && $v->Delivery_instruction != '') {
    //             $status .= 'JO - ' . $v->Ticket_Status;
    //         } else {
    //             $status .= 'JMF - ' . $v->Ticket_Status;
    //         }
    //         $dataAjax[$no][] = $status;
    //         $no++;
    //     }
    //     $totalRecord = $this->totalRecord();
    //     $totalRecordFiltered = $this->totalRecord(true);
    //     $response = [
    //         // "draw" => $offset,
    //         "recordsFiltered" => $search != '' ? $totalRecordFiltered : $totalRecord,
    //         "recordsTotal" => $totalRecord,
    //         "data"  => $dataAjax,
    //         "error" => '',
    //     ];


    //     echo json_encode($response);
    // }
}
