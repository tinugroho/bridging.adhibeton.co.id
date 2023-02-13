<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Selawah extends CI_Controller
{
    public function index()
    {

        $data = [
            'judul'         => 'Pabrik Aceh',
            'user'          => $this->Model_auth->dataLogin()
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('aceh/index', $data);
        $this->load->view('templates/footer');
    }
    public function detail_load()
    {
        $data = [
            'judul'         => 'Detail load',
            'user'          => $this->Model_auth->dataLogin()
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('aceh/detail_load', $data);
        $this->load->view('templates/footer');
    }
    public function detail_batch()
    {
        $data = [
            'judul'         => 'Detail batch',
            'user'          => $this->Model_auth->dataLogin()
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('aceh/detail_batch', $data);
        $this->load->view('templates/footer');
    }
    public function contoh()
    {
        $data = [
            'judul'         => 'Detail batch',
            'user'          => $this->Model_auth->dataLogin()
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('aceh/contoh', $data);
        $this->load->view('templates/footer');
    }
}
