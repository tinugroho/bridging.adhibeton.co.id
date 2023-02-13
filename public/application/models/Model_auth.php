<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_auth extends CI_Model
{
    public function tambahRegister()
    {
        $data = [
            'username'          => $this->input->post('username'),
            'email'             => $this->input->post('email'),
            'password'          => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'image'             => 'default.jpg',
            'id_role'           => 2,
            // 'id_role'           => $this->input->post('id_role'),
            'date_created'      => time(),
            'no_hp'             => $this->input->post('no_hp'),
            'is_active'         => 1
        ];

        $this->db->insert('tb_user', $data);
    }

    public function dataLogin()
    {

        $role  = $this->session->userdata('id_role');
        $email = $this->session->userdata('email');

        $this->db->select('tb_user.*, tb_role.role');
        $this->db->from('tb_user');
        $this->db->join('tb_role', 'tb_role.id = tb_user.id_role');
        $this->db->where('tb_user.id_role', $role);
        $this->db->where('tb_user.email', $email);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function dataUsers()
    {
        $role  = $this->session->userdata('id_role');
        $email = $this->session->userdata('email');

        $this->db->select('tb_user.*, tb_role.role');
        $this->db->from('tb_user');
        $this->db->join('tb_role', 'tb_role.id = tb_user.id_role');
        $query = $this->db->get();
        return $query->result_array();
    }

    //     public function userData()
    //     {
    //         $role = $this->session->userdata('role_id');
    //         $query = "SELECT `user`.*, `user_role`.`role`
    //         FROM  `user` JOIN `user_role`
    //            ON `user`.`role_id` = `user_role`.`id`
    //            WHERE `user`.`role_id` = $role


    // ";

    //         return $this->db->query($query, ['email' => $this->session->userdata('email')])->row_array();
    //     }
}
