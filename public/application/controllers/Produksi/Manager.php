<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Manager extends CI_Controller
{
    public function index()
    {
        $data = [
            'judul'         => 'Dashborad'
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar');
        $this->load->view('admin/dashboard');
        $this->load->view('templates/footer');
    }
}
