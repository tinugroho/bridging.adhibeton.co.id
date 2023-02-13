<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends CI_Controller
{
    public function index()
    {

        $data = [
            'judul'         => 'View Profile',
            'user'          => $this->Model_auth->dataLogin()
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('profile/edit', $data);
        $this->load->view('templates/footer');
    }

    public function edit()
    {
        $data = [
            'judul'         => 'Setting',
            'user'          => $this->Model_auth->dataLogin()
        ];
        $this->form_validation->set_rules('username', 'Username', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/topbar');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('profile/validation', $data);
            $this->load->view('templates/footer');
        } else {

            $username = $this->input->post('username');
            $email = $this->input->post('email');

            //gambar
            $upload_image = $_FILES['image']['name'];

            if ($upload_image) {
                $config['allowed_types'] = 'jpg|gif|png';
                $config['max_size'] = '2048';
                $config['upload_path'] = './assets/img/';

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('image')) {

                    $imageLama = $data['user']['image'];
                    if ($imageLama != 'default.jpg') {
                        unlink(FCPATH . 'assets/img/' . $imageLama);
                    }


                    $new_image = $this->upload->data('file_name');
                    $this->db->set('image', $new_image);
                } else {
                    echo $this->upload->display_errors();
                }
            }

            $this->db->set('username', $username);
            $this->db->where('email', $email);
            $this->db->update('tb_user');
            $this->session->set_flashdata('pesan', '<div class="alert alert-primary" role="alert">
            Congratulation ! your updating profile
          </div>');
            redirect('profile/edit');
        }
    }
}
