<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        cek_login();
    }

    public function index()
    {
        $data = [
            'judul'         => 'Dashboard',
            'user'          => $this->Model_auth->dataLogin()
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('admin/dashboard');
        $this->load->view('templates/footer');
    }

    public function coba()
    {
        $data = [
            'judul'         => 'coba'
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar');
        // $this->load->view('admin/dashboard');
        $this->load->view('templates/footer');
    }
}
