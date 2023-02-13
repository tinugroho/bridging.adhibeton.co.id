<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sadang extends CI_Controller
{

    public function index()
    {

        $data = [
            'judul'         => 'Pabrik Sadang',
            'user'          => $this->Model_auth->dataLogin(),
            'view'          => $this->Model_produksi->viewSadang()
        ];

        $uri = $this->uri->segment(3);

        if ($this->uri->segment(3) === false) {
            echo "0";
        } else {
            $uri = $this->uri->segment(3);
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('produksi/sadang/index', $data);
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
        $this->load->view('produksi/sadang/detail_load', $data);
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
        $this->load->view('produksi/sadang/detail_batch', $data);
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
