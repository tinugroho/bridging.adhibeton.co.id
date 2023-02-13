<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api extends CI_Controller
{

    public function active()
    {
        // $live_bspi  = $this->getApi('live-bspi', "http://10.0.17.20:8090/apberp/erp/eaccounting/tools/api/sfservice.cfc?method=getDataBSPI");

        // $dev_bspi   = $this->getApi('dev-bspi', "http://10.0.17.20:8090/apberpdev/erp/eaccounting/tools/api/sfservice.cfc?method=getDataBSPI");

        // $dev_bsp    = $this->getApi('dev-bsp', "http://10.0.17.20:8090/apberpdev/erp/eaccounting/tools/api/sfservice.cfc?method=getDataBSP2");

        // $all_bsp    = array_merge($live_bspi, $dev_bspi, $dev_bsp);
        // usort($all_bsp, function ($a, $b) {
        //     return $b['JO_Date'] <=> $a['JO_Date'];
        // });

        $data = [
            'judul'         => 'Api Active',
            'user'          => $this->Model_auth->dataLogin(),
            'data'          => $this->Model_api->active()
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('api/active', $data);
        $this->load->view('templates/footer');
    }

    // public function getApi($type, $url)
    // {
    //     //  Initiate curl
    //     $ch = curl_init();
    //     // Will return the response, if false it print the response
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     // Set the url
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     // Execute
    //     $result = curl_exec($ch);
    //     // Closing
    //     curl_close($ch);

    //     // Will dump a beauty json :3
    //     $result = json_decode($result, true);
    //     // var_dump($result);
    //     if (!empty(reset($result))) {
    //         $result = reset($result);
    //         foreach ($result as $key => $val) {
    //             $result[$key]['type'] = $type;

    //             $result[$key]['post_sukses'] = $this->Model_api->countPostSukses($type, $val['JO_Number']);
    //             $result[$key]['post_gagal'] = $this->Model_api->countPostGagal($type, $val['JO_Number']);
    //         }
    //     }
    //     return $result;
    // }


    public function sukses()
    {
        $type = $this->input->get('type');
        $JO_Number = $this->input->get('JO_Number');
        $data = [
            'judul'         => 'Api Sukses',
            'user'          => $this->Model_auth->dataLogin(),
            'JO_Number'     => $JO_Number,
            'type'          => $type,
            'data'          => $this->Model_api->postSukses($type, $JO_Number)
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('api/post-sukses', $data);
        $this->load->view('templates/footer');
    }

    public function detail()
    {
        $type = $this->input->get('type');
        $Index_Log = $this->input->get('Index_Log');
        $mesin = $this->input->get('mesin');
        $detailPostGagal = $this->Model_api->detailPostSukses($Index_Log, $mesin);
        $detailPostGagal = !empty($detailPostGagal) ? $detailPostGagal[0] : '';

        $Running_Date = empty($detailPostGagal) ? '' : $detailPostGagal->Method_Date;
        $RecordDate = empty($detailPostGagal) ? '' : $detailPostGagal->CreateDateBP;
        $Keterangan = empty($detailPostGagal) ? '' : $detailPostGagal->Keterangan;
        $Index_Load = empty($detailPostGagal) ? '' : $detailPostGagal->Index_Load;
        $Load_Size = empty($detailPostGagal) ? '' : $detailPostGagal->Post_Qty;
        $JO_Number = empty($detailPostGagal) ? '' : $detailPostGagal->JO_Number;
        $Material = empty($detailPostGagal) ? '' : $detailPostGagal->Material;
        $type = empty($detailPostGagal) ? '' : $detailPostGagal->type;

        $data = [
            'judul'         => 'Api Detail',
            'user'          => $this->Model_auth->dataLogin(),
            'Running_Date'  => $Running_Date,
            'RecordDate'    => $RecordDate,
            'Keterangan'    => $Keterangan,
            'Index_Load'    => $Index_Load,
            'Load_Size'     => $Load_Size,
            'JO_Number'     => $JO_Number,
            'Material'      => $Material,
            'type'          => $type,
            'mesin'          => $mesin,
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('api/detail', $data);
        $this->load->view('templates/footer');
    }

    public function gagal()
    {
        $type = $this->input->get('type');
        $JO_Number = $this->input->get('JO_Number');
        $data = [
            'judul'         => 'Api Gagal',
            'user'          => $this->Model_auth->dataLogin(),
            'JO_Number'     => $JO_Number,
            'type'          => $type,
            'data'          => $this->Model_api->postGagal($type, $JO_Number)
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('api/post-gagal', $data);
        $this->load->view('templates/footer');
    }

    public function repost()
    {
        $type = $this->input->get('type');
        $Index_Post_Gagal = $this->input->get('Index_Post_Gagal');
        $mesin = $this->input->get('mesin');
        $detailPostGagal = $this->Model_api->detailPostGagal($Index_Post_Gagal, $mesin);
        $detailPostGagal = !empty($detailPostGagal) ? $detailPostGagal[0] : '';
        $alert = '';
        $warna = '';

        $data_jo = $this->getDataJO($type);

        if (isset($_POST['repost'])) {

            $Input_JO_Number = $this->input->post('JO_Number');
            $Input_Material = $this->input->post('Material');
            $Input_Amount = $this->input->post('amount');
            $Str_Material = '';

            foreach ($Input_Material as $key => $val) {
                $Str_Material .= '-' . $val . '|' . $Input_Amount[$key];
            }
            $Str_Material = !empty($Str_Material) ? substr($Str_Material, 1) : '';
            $detailPostGagal = $this->Model_api->repost($type, $Index_Post_Gagal, $Input_JO_Number, $Str_Material, $mesin);

            if ($detailPostGagal->Keterangan == 'POSTED') {
                $alert = 'Post Sukses';
                $warna = 'success';
            } else {
                $alert = 'Terjadi Kesalahan. Return JSON API: ' . $detailPostGagal->Keterangan;
                $warna = 'danger';
            }
        } else if (isset($_POST['update'])) {

            $Input_JO_Number = $this->input->post('JO_Number');
            $Input_Material = $this->input->post('Material');
            $Input_Amount = $this->input->post('amount');
            $Str_Material = '';

            foreach ($Input_Material as $key => $val) {
                $Str_Material .= '-' . $val . '|' . $Input_Amount[$key];
            }
            $Str_Material = !empty($Str_Material) ? substr($Str_Material, 1) : '';

            $key_jo = array_search($Input_JO_Number, array_column($data_jo, 'JO_Number'));
            $SO_Number = '';
            if ($key_jo !== false) {
                $SO_Number = @$data_jo[$key_jo]['SO_Number'];
            }

            $detailPostGagal = $this->Model_api->updatePostGagal($Index_Post_Gagal, $Input_JO_Number, $SO_Number, $Str_Material, $mesin);
        } else if (isset($_POST['delete'])) {
            $deletePostGagal = $this->Model_api->deletePostGagal($Index_Post_Gagal, $mesin);
            if ($deletePostGagal) {
                header("Location:" . base_url('Api/post') . "?type=$type&Index_Load=" . $detailPostGagal->Index_Load . "&mesin=$mesin");
            }
        }

        $Running_Date = empty($detailPostGagal) ? '' : $detailPostGagal->Running_Date;
        $RecordDate = empty($detailPostGagal) ? '' : $detailPostGagal->RecordDate;
        $Keterangan = empty($detailPostGagal) ? '' : $detailPostGagal->Keterangan;
        $Index_Load = empty($detailPostGagal) ? '' : $detailPostGagal->Index_Load;
        $Load_Size = empty($detailPostGagal) ? '' : $detailPostGagal->Load_Size;
        $JO_Number = empty($detailPostGagal) ? '' : $detailPostGagal->JO_Number;
        $Material = empty($detailPostGagal) ? '' : $detailPostGagal->Material;
        $type = empty($detailPostGagal) ? '' : $detailPostGagal->type;

        $data = [
            'judul'         => 'Api Repost',
            'user'          => $this->Model_auth->dataLogin(),
            'Running_Date'  => $Running_Date,
            'RecordDate'    => $RecordDate,
            'Keterangan'    => $Keterangan,
            'Index_Load'    => $Index_Load,
            'Load_Size'     => $Load_Size,
            'JO_Number'     => $JO_Number,
            'Material'      => $Material,
            'type'          => $type,
            'mesin'          => $mesin,
            'detailPostGagal' => $detailPostGagal,
            'alert'         => $alert,
            'warna'         => $warna,
            'materialPerLoad' => $this->Model_api->materialPerLoad($Index_Load, $mesin),
            'data_jo'       =>  $data_jo,
            'data_density'  =>  $this->getDataDensity($type),
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('api/repost', $data);
        $this->load->view('templates/footer');
        // print_r($this->getDataDensity($type));
    }
    public function post()
    {
        $type = $this->input->get('type');
        $Index_Load = $this->input->get('Index_Load');
        $mesin = $this->input->get('mesin');
        $detailLoad = $this->Model_api->detailLoad($Index_Load, $mesin);
        $detailLoad = !empty($detailLoad) ? $detailLoad[0] : '';
        $alert = '';
        $warna = '';

        if (isset($_POST['post'])) {

            $JO_Number = $this->input->post('JO_Number');
            $data_jo = $this->input->post('data_jo');
            $Input_Material = $this->input->post('Material');
            $Input_Amount = $this->input->post('amount');
            $Str_Material = '';

            foreach ($Input_Material as $key => $val) {
                $Str_Material .= '-' . $val . '|' . $Input_Amount[$key];
            }
            $Str_Material = !empty($Str_Material) ? substr($Str_Material, 1) : '';
            // echo $Str_Material;
            $detailLoad = $this->Model_api->post($type, $Index_Load, $JO_Number, $data_jo, $Str_Material, $mesin);

            if ($detailLoad->Keterangan == 'sukses') {
                $alert = 'Post Sukses';
                $warna = 'success';
            } else {
                $alert = 'Terjadi Kesalahan. Return JSON API: ' . $detailLoad->Keterangan;
                $warna = 'danger';
            }
        } else if (isset($_POST['update'])) {

            $JO_Number = $this->input->post('JO_Number');
            $data_jo = $this->input->post('data_jo');
            $Input_Material = $this->input->post('Material');
            $Input_Amount = $this->input->post('amount');
            $Str_Material = '';

            foreach ($Input_Material as $key => $val) {
                $Str_Material .= '-' . $val . '|' . $Input_Amount[$key];
            }
            $Str_Material = !empty($Str_Material) ? substr($Str_Material, 1) : '';

            $indexPostGagal = $this->Model_api->insertPostGagal($JO_Number, $data_jo, $type, $Str_Material, $detailLoad, $mesin);
            if ($indexPostGagal != 0) {
                header("Location:" . base_url('Api/repost') . "?type=$type&Index_Post_Gagal=$indexPostGagal&mesin=$mesin");
            }
        }

        if ($mesin == 'commandbatch') {
            $RecordDate = empty($detailLoad) ? '' : $detailLoad->RecordDate;
            $Load_Size = empty($detailLoad) ? '' : $detailLoad->Load_Size;
            $JO_Number = empty($detailLoad) ? '' : $detailLoad->Delivery_Instruction;
        } else if ($mesin == 'autobatch') {
            $RecordDate = empty($detailLoad) ? '' : $detailLoad->Createdate;
            $Load_Size = empty($detailLoad) ? '' : $detailLoad->Qty_Jobmix;
            $JO_Number = empty($detailLoad) ? '' : $detailLoad->Jo_Number_by_column;
        } else {
            $RecordDate = '';
            $Load_Size = '';
            $JO_Number = '';
        }

        $data = [
            'judul'         => 'Api Repost',
            'user'          => $this->Model_auth->dataLogin(),
            'RecordDate'    => $RecordDate,
            'Index_Load'    => $Index_Load,
            'Load_Size'     => $Load_Size,
            'JO_Number'     => $JO_Number,
            'type'          => $type,
            'mesin'          => $mesin,
            'alert'         => $alert,
            'warna'         => $warna,
            'dataLoad' => $detailLoad,
            'materialPerLoad' => $this->Model_api->materialPerLoad($Index_Load, $mesin),
            'data_jo'       =>  $this->getDataJO($type),
            'data_density'  =>  $this->getDataDensity($type),
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('api/post', $data);
        $this->load->view('templates/footer');
        // print_r($this->getDataDensity($type));
    }

    public function getDataJO($type)
    {
        switch ($type) {
            case 'live-bspi':
                $url = "http://192.168.100.17/apberp/erp/eaccounting/tools/api/sfservice.cfc?method=getDataBSPI";
                break;
            case 'dev-bspi':
                $url = "http://192.168.100.17/apberpdev/erp/eaccounting/tools/api/sfservice.cfc?method=getDataBSPI";
                break;
            case 'live-bsp':
                $url = "http://192.168.100.17/apberp/erp/eaccounting/tools/api/sfservice.cfc?method=getDataBSP2";
                break;
            case 'dev-bsp':
                $url = "http://192.168.100.17/apberpdev/erp/eaccounting/tools/api/sfservice.cfc?method=getDataBSP2";
                break;

            default:
                return;
                break;
        }

        //  Initiate curl
        $ch = curl_init();
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Set the url
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds
        // Execute
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return [];
        }
        // Closing
        curl_close($ch);

        // Will dump a beauty json :3
        $result = json_decode($result, true);
        // var_dump($result);

        switch ($type) {
            case 'live-bspi':
                return $result['databsp'];
                break;
            case 'dev-bspi':
                return $result['databsp'];
                break;
            case 'live-bsp':
                return $result['databsp2'];
                break;
            case 'dev-bsp':
                return $result['databsp2'];
                break;

            default:
                return;
                break;
        }
    }

    public function getDataDensity($type)
    {
        $devOrLive = explode('-', $type)[0];
        $devOrLive = $devOrLive == 'dev' ? 'dev' : '';
        ///aray konversi dari api
        $urlKonversi = "http://192.168.100.17/apberp$devOrLive/erp/eaccounting/tools/api/sfservice.cfc?method=getDataDensity";
        //  Initiate curl
        $chKonversi = curl_init();
        // Will return the response, if false it print the response
        curl_setopt($chKonversi, CURLOPT_RETURNTRANSFER, true);
        // Set the url
        curl_setopt($chKonversi, CURLOPT_URL, $urlKonversi);
        curl_setopt($chKonversi, CURLOPT_TIMEOUT, 10); //timeout in seconds
        // Execute
        $resultKonversi = curl_exec($chKonversi);
        if (curl_errno($chKonversi)) {
            return [];
        }
        // Closing
        curl_close($chKonversi);

        // Will dump a beauty json :3
        $resultKonversi = json_decode($resultKonversi, true);
        $arrKonversi = $resultKonversi['dataitem'];
        // var_dump($arrKonversi);

        if (!isset($resultKonversi['dataitem'])) {
            return 'getDataDensity gagal';
        }
        return $arrKonversi;
    }

    public function history()
    {
        $data = [
            'judul'         => 'Api History',
            'user'          => $this->Model_auth->dataLogin(),
            // 'data'          => $this->Model_api->history()
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('api/history', $data);
        echo "<script> var id_region = '" . $this->uri->segment(3) . "'; </script>";
        echo "<script> var mesin = ''; </script>";
        $this->load->view('templates/footer');
    }

    // AJAX
    public function totalRecord($filter = false)
    {

        $tglStart = $this->input->get('tglStart');
        $tglEnd = $this->input->get('tglEnd');

        if ($filter) {
            $search = $this->input->get('search');
            $search =  $search['value'];
        } else {
            $search = '';
        }
        return $this->Model_api->totalRecord($tglStart, $tglEnd, $search);
    }

    public function ajaxHistory()
    {
        $tglStart = $this->input->get('tglStart');
        $tglEnd = $this->input->get('tglEnd');

        $length = $this->input->get('length');
        $offset = $this->input->get('start');

        $search =  $this->input->get('search');
        $search =  $search['value'];


        $column_ref = ['Running_Date', 'JO_Number', 'Jobmix_ERP', 'Jobmix_BP', 'Vol', 'RecordDate', 'Running_Date', 'Index_Load', 'Keterangan', 'status'];
        $order = $this->input->get('order');
        $order = $order[0];
        $order['column'] = $column_ref[$order['column']];


        $data = $this->Model_api->history($tglStart, $tglEnd, $length, $offset, $search, $order);

        $dataAjax = array();
        $no = 0;
        foreach ($data as $v) {

            $dataAjax[$no][] = $no + 1 + $offset;
            $dataAjax[$no][] = $v->JO_Number;
            $dataAjax[$no][] = $v->Jobmix_ERP;
            $dataAjax[$no][] = $v->Jobmix_BP;
            $dataAjax[$no][] = $v->Vol;
            $dataAjax[$no][] = empty($v->RecordDate) ? '' : date_format(date_create($v->RecordDate), "Y-m-d <\b\\r> H:i");
            $dataAjax[$no][] = date_format(date_create($v->Running_Date), "Y-m-d <\b\\r> H:i");
            $dataAjax[$no][] = $v->Index_Load;
            $dataAjax[$no][] = $v->Keterangan;
            $dataAjax[$no][] = $v->status;
            $dataAjax[$no][] = $v->status == 'sukses' ?
                '<form target="_blank" action="' . base_url('Api/detail') . '" method="GET">
                                    <input type="hidden" name="type" value="' . $v->type . '">
                                    <input type="hidden" name="Index_Log" value="' . $v->Index_Log . '">
                                    <input type="hidden" name="mesin" value="' . $v->db . '">
                                    <button type="submit" class="btn btn-info badge">
                                        Detail
                                    </button>
                                </form>'
                :
                '<form target="_blank" action="' . base_url('Api/repost') . '" method="GET">
                                        <input type="hidden" name="type" value="' . $v->type . '">
                                        <input type="hidden" name="Index_Post_Gagal" value="' . $v->Index_Log . '">
                                        <input type="hidden" name="mesin" value="' . $v->db . '">
                                        <button type="submit" class="btn btn-info badge">
                                            ' . ($v->Keterangan != 'POSTED' ? 'Repost' : 'Detail') . '
                                        </button>
                                    </form>';
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
