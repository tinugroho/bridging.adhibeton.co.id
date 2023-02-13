<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sklp extends CI_Controller
{

    public function history()
    {

        $start = $this->input->get('start');
        $end = $this->input->get('end');

        // record date
        if ($start == '' && $end == '') {
            $record_date = " and b.RecordDate BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() + INTERVAL 1 DAY ";
        } else {
            if ($start == '') {
                $start = "2018-01-01 00:00";
            }
            if ($end == '') {
                $end = "CURDATE() + INTERVAL 1 DAY";
            } else {
                $end = "'$end'";
            }

            $record_date = " and b.RecordDate BETWEEN '" . $start . "' AND " . $end . " ";
        }
        // record date

        $data = [
            'judul'         => 'SKLP History',
            'user'          => $this->Model_auth->dataLogin(),
            'data'          => $this->db->query("select a.args, a.tgl_post, b.* , count(b.index_load) x_load, sum(b.Load_Size) cummulative, max(a.tgl_post) last_post,
                                                    case when b.Item_Code is not null then b.Item_Code else b.Other_Code end Jobmix
                                                from SKLP_API_Log a 
                                                inner join V_BatchSetupTickets b on a.ref=b.index_load $record_date
                                                group by b.PO_Num , case when b.Item_Code is not null then b.Item_Code else b.Other_Code end, b.Consistence
                                                order by last_post desc"),
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);


        $this->load->view('sklp/history', $data);

        echo "<script> var id_region = '" . $this->uri->segment(3) . "'; </script>";
        echo "<script> var mesin = ''; </script>";
        $this->load->view('templates/footer');
    }
    public function detail()
    {
        $start = $this->input->get('start');
        $end = $this->input->get('end');

        // record date
        if ($start == '' && $end == '') {
            $record_date = " and b.RecordDate BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() + INTERVAL 1 DAY ";
        } else {
            if ($start == '') {
                $start = "2018-01-01 00:00";
            }
            if ($end == '') {
                $end = "CURDATE() + INTERVAL 1 DAY";
            } else {
                $end = "'$end'";
            }

            $record_date = " and b.RecordDate BETWEEN '" . $start . "' AND " . $end . " ";
        }
        // record date
        $PO_Num = $this->input->get('PO_Num');
        $Jobmix = $this->input->get('Jobmix');
        $Consistence = $this->input->get('Consistence');
        $data = [
            'judul'         => 'SKLP Detail',
            'user'          => $this->Model_auth->dataLogin(),
            'data'          => $this->db->query("select a.args, a.tgl_post, a.status, b.* , case when b.Item_Code is not null then b.Item_Code else b.Other_Code end Jobmix
                                                from SKLP_API_Log a 
                                                inner join V_BatchSetupTickets b on a.ref=b.index_load
                                                where b.PO_Num='$PO_Num' and case when b.Item_Code is not null then b.Item_Code='$Jobmix' else b.Other_Code='$Jobmix' end and b.Consistence='$Consistence' $record_date
                                                order by tgl_post desc"),
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);


        $this->load->view('sklp/detail', $data);

        echo "<script> var id_region = '" . $this->uri->segment(3) . "'; </script>";
        echo "<script> var mesin = ''; </script>";
        $this->load->view('templates/footer');
    }
}
