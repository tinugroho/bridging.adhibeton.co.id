<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{


    public function index()
    {

        if ($this->input->post('id') != '') {
            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('id_role', 'Id Role', 'required');
            if ($this->input->post('id') == 0) {
                $this->form_validation->set_rules('email', 'Email', 'required');
                $this->form_validation->set_rules('password', 'Password', 'required');
                if ($this->form_validation->run() == true) {
                    $user = $this->db->get_where('tb_user', ['email' => $this->input->post('email')])->row_array();
                    if (empty($user)) {
                        if ($this->Model_user->addUser()) {
                            $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Add User Success!</div>');
                        } else {
                            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Add User Failed!</div>');
                        }
                    } else {
                        $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Email sudah digunakan!</div>');
                    }
                } else {
                    $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Silahkan lengkapi data user.</div>');
                }
            } else {
                if ($this->form_validation->run() == true) {
                    if ($this->Model_user->editUser()) {
                        $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">User updated!</div>');
                    } else {
                        $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Update User Failed!</div>');
                    }
                } else {
                    $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Silahkan lengkapi data user.</div>');
                }
            }
        }
        $data_users = $this->Model_auth->dataUsers();
        foreach ($data_users as $key => $val) {
            $arr_access = [];
            foreach ($this->Model_user->userAccessRegion($val['id']) as $valregion) {
                // array_push($arr_access,$valregion['id_region']);
                $arr_access[] = $valregion['id_region'];
            }
            $data_users[$key]['access_region'] = json_encode($arr_access);
        }

        $data = [
            'judul'         => 'Data Users',
            'user'          => $this->Model_auth->dataLogin(),
            'users'         => $data_users,
            'all_regional'  => $this->Model_region->allRegion(),
            'role'          => $this->Model_menu->getRole()
        ];



        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('user/index', $data);
        $this->load->view('templates/footer');
        // echo '<pre>';
        // print_r($_SESSION);
        // echo '</pre>';
    }
}
