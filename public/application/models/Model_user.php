<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_user extends CI_Model
{
    public function editUser()
    {
        $data = [
            'username' => $this->input->post('username'),
            'id_role' => $this->input->post('id_role')
        ];
        if ($this->input->post('password') != '') {
            $data['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
        }

        $this->db->where('id', $this->input->post('id'));
        $edit_sukses = $this->db->update('tb_user', $data);
        if ($edit_sukses) {
            $this->db->where('id_user', $this->input->post('id'));

            if ($this->db->delete('tb_access_region')) {
                foreach ($this->input->post('region') as $key => $val) {
                    $access = [
                        'id_user'   => $this->input->post('id'),
                        'id_region' => $val,
                    ];

                    $this->db->insert('tb_access_region', $access);
                }
            }
        }
        return $edit_sukses;
    }
    public function addUser()
    {
        $data = [
            'username'        => $this->input->post('username'),
            'email'        => $this->input->post('email'),
            'password'          => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'id_role'       => $this->input->post('id_role'),
            'date_created'       => time(),
            // 'no_hp'             => $this->input->post('no_hp'),
            'is_active'       => 1,
        ];

        return $this->db->insert('tb_user', $data);
    }
    public function userAccessRegion($id_user)
    {
        $this->db->select('id_region');
        $this->db->from('tb_access_region');
        $this->db->where('id_user', $id_user);
        $result = $this->db->get();
        return $result->result_array();
    }
}
