<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_menu extends CI_Model
{
    // menu utama model 
    public function editMenu()
    {
        $data = [
            'menu' => $this->input->post('menu')
        ];

        $this->db->where('id', $this->input->post('id'));
        $this->db->update('tb_menu', $data);
    }
    // end menu utama

    // menu list model
    public function menuList()
    {
        $this->db->select('tb_menu.menu, tb_menu_list.*, Region.region_name');
        $this->db->from('tb_menu_list');
        $this->db->join('tb_menu', 'tb_menu.id = tb_menu_list.id_menu');
        $this->db->join('Region', 'tb_menu_list.binding_id_region=Region.id_region', 'left');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function tambahMenuList()
    {
        $data = [
            'nama_menu' => $this->input->post('nama_menu'),
            'url'       => $this->input->post('url'),
            'icon'      => $this->input->post('icon'),
            'id_menu'   => $this->input->post('id_menu')
        ];
        if ($this->input->post('id_region') != '') {
            $data['binding_id_region'] = $this->input->post('id_region');
        }

        $this->db->insert('tb_menu_list', $data);
    }

    public function editMenuList()
    {
        $data = [
            'nama_menu' => $this->input->post('nama_menu'),
            'url'       => $this->input->post('url'),
            'icon'      => $this->input->post('icon'),
            'id_menu'   => $this->input->post('id_menu')
        ];
        if ($this->input->post('id_region') != '') {
            $data['binding_id_region'] = $this->input->post('id_region');
        }

        $this->db->where('id', $this->input->post('id'));
        $this->db->update('tb_menu_list', $data);
    }
    // end model menu list

    // modal submenu 
    public function subMenu()
    {
        $this->db->select('tb_submenu.*, tb_menu_list.nama_menu, tb_menu.id category_id, tb_menu.menu, Region.region_name',);
        $this->db->from('tb_submenu');
        $this->db->join('tb_menu_list', 'tb_menu_list.id = tb_submenu.id_menu_list');
        $this->db->join('tb_menu', 'tb_menu_list.id_menu = tb_menu.id', 'left');
        $this->db->join('Region', 'tb_submenu.binding_id_region=Region.id_region', 'left');
        $this->db->order_by('tb_menu.menu', 'asc');
        $this->db->order_by('tb_menu_list.nama_menu', 'asc');
        $this->db->order_by('tb_submenu.order_by', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function tambahSubmenu()
    {
        $data = [
            'submenu'        => $this->input->post('submenu'),
            'url_sub'        => $this->input->post('url_sub'),
            'id_menu_list'   => $this->input->post('id_menu_list'),
            'order_by'       => $this->input->post('order_by')
        ];
        if ($this->input->post('id_region') != '') {
            $data['binding_id_region'] = $this->input->post('id_region');
        }

        $this->db->insert('tb_submenu', $data);
    }

    public function editSubMenu()
    {
        $data = [
            'submenu'        => $this->input->post('submenu'),
            'url_sub'        => $this->input->post('url_sub'),
            'id_menu_list'   => $this->input->post('id_menu_list'),
            'order_by'       => $this->input->post('order_by')
        ];
        if ($this->input->post('id_region') != '') {
            $data['binding_id_region'] = $this->input->post('id_region');
        }

        $this->db->where('id', $this->input->post('id'));
        $this->db->update('tb_submenu', $data);
    }

    public function getRole($id_role = '')
    {
        if ($id_role == '') {
            return $this->db->get('tb_role')->result_array();
        } else {
            $this->db->where('id ==', $id_role);
            return $this->db->get('tb_role')->result_array();
        }
    }

    public function editRole()
    {
        $data = [
            'role' => $this->input->post('role')
        ];

        $this->db->where('id', $this->input->post('id'));
        $this->db->update('tb_role', $data);
    }

    public function roleMenu()
    {
        $this->db->where('id !=', 1);
        return $this->db->get('tb_menu')->result_array();
    }
}
