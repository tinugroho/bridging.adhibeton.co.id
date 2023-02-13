<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Icon extends CI_Controller
{
    public function index()
    {
        $data = [
            'judul'         => 'Icons',
            'user'          => $this->Model_auth->dataLogin(),
            'menu'          => $this->db->get('tb_menu')->result_array()
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('admin/icon/view-icon', $data);
        $this->load->view('templates/footer');
    }
}
