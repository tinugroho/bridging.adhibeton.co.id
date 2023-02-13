<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        cek_login();
    }

    // Menu utama controler
    public function menuUtama()
    {
        $data = [
            'judul'         => 'Menu Utama',
            'user'          => $this->Model_auth->dataLogin(),
            'menu'          => $this->db->get('tb_menu')->result_array(),

        ];

        $this->form_validation->set_rules('menu', 'Menu', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/topbar');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('admin/menu-utama/view-menu-utama', $data);
            $this->load->view('templates/footer');
        } else {
            $this->db->insert('tb_menu', ['menu' => $this->input->post('menu')]);
            $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">
            successfully added new data
             </div>');
            redirect('Admin/menu/menuutama');
        }
    }

    public function editMenu($id)
    {
        $this->Model_menu->editMenu($id);
        $this->session->set_flashdata('pesan', '<div class="alert alert-warning" role="alert">
        successfully edited data
       </div>');
        redirect('Admin/menu/menuutama');
    }

    public function hapusMenuUtama($id)
    {

        $this->db->where('id', $id);
        $this->db->delete('tb_menu');

        $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">
        successfully deleted data
         </div>');
        redirect('Admin/menu/menuutama');
    }
    // end menu utama

    // menu controler
    public function menuList()
    {
        $data = [
            'judul'         => 'Menu List',
            'user'          => $this->Model_auth->dataLogin(),
            'region'          => $this->db->get('Region')->result_array(),
            'menu'          => $this->db->get('tb_menu')->result_array(),
            'menulist'      => $this->Model_menu->MenuList()
        ];

        $this->form_validation->set_rules('nama_menu', 'Name Menu', 'required|trim');
        $this->form_validation->set_rules('url', 'Url', 'required|trim');
        $this->form_validation->set_rules('icon', 'Icon', 'required|trim');


        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/topbar');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('admin/menu-list/view-menu-list', $data);
            $this->load->view('templates/footer');
        } else {
            $this->Model_menu->tambahMenuList();
            $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">
            successfully added new data
             </div>');
            redirect('Admin/menu/menulist');
        }
    }

    public function editMenuList()
    {
        $this->Model_menu->editMenuList();
        $this->session->set_flashdata('pesan', '<div class="alert alert-warning" role="alert">
        successfully edited data
       </div>');
        redirect('Admin/menu/menulist');
    }

    public function hapusMenuList($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tb_menu_list');

        $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">
        successfully deleted data
         </div>');
        redirect('Admin/menu/menulist');
    }
    // end menu list

    // submenu controler
    public function submenu()
    {
        $data = [
            'judul'         => 'Submenu',
            'user'          => $this->Model_auth->dataLogin(),
            'submenu'       => $this->Model_menu->subMenu(),
            'region'          => $this->db->get('Region')->result_array(),
        ];

        $this->form_validation->set_rules('submenu', 'Name subMenu', 'required|trim');
        $this->form_validation->set_rules('url_sub', 'Url', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/topbar');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('admin/submenu/view-submenu', $data);
            $this->load->view('templates/footer');
        } else {
            $this->Model_menu->tambahSubmenu();
            $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">
          successfully added new data
           </div>');
            redirect('Admin/menu/submenu');
        }
    }

    public function editSubmenu()
    {
        $this->Model_menu->editSubMenu();
        $this->session->set_flashdata('pesan', '<div class="alert alert-warning" role="alert">
        successfully edited data
       </div>');
        redirect('Admin/menu/submenu');
    }

    public function hapusSubmenu($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tb_submenu');

        $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">
        successfully deleted data
         </div>');
        redirect('Admin/menu/submenu');
    }
    // end submenu

    // role access controler
    public function role()
    {
        $data = [
            'judul'         => 'Access Menu',
            'user'          => $this->Model_auth->dataLogin(),
            'role'          => $this->db->get('tb_role')->result_array(),
            'menu'          => $this->Model_menu->roleMenu()

        ];

        $this->form_validation->set_rules('role', 'Role', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/topbar');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('admin/access-role/view-role', $data);
            $this->load->view('templates/footer');
        } else {
            $this->db->insert('tb_role', ['role' => $this->input->post('role')]);
            $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">
            successfully added new data
             </div>');
            redirect('Admin/menu/role');
        }
    }

    public function editRole($id)
    {
        $this->Model_menu->editRole($id);
        $this->session->set_flashdata('pesan', '<div class="alert alert-warning" role="alert">
        successfully edited data
       </div>');
        redirect('Admin/menu/role');
    }


    public function hapusRole($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tb_role');

        $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">
        successfully deleted data
         </div>');
        redirect('Admin/menu/role');
    }

    public function gantiaccess()
    {
        $id_menu = $this->input->post('id_menu');
        $id_role = $this->input->post('id_role');

        $data = [
            'id_role' => $id_role,
            'id_menu'   => $id_menu
        ];

        $result = $this->db->get_where('tb_access_menu', $data);

        if ($result->num_rows() < 1) {
            $this->db->insert('tb_access_menu', $data);
        } else {
            $this->db->delete('tb_access_menu', $data);
        }

        $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">
        Access has been replaced
         </div>');
    }
    // end role access
}
