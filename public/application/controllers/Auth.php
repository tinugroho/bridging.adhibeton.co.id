<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function index()
    {
        $ref = $this->input->get('r');

        // cek yg sedang login

        $data = [
            'judul'         => 'Login Page',
            'r'             => $ref
        ];

        $this->form_validation->set_rules(
            'email',
            'email',
            'required|trim|valid_email',
            [
                'required'          => 'Email is required',
                'valid_email'       => 'The email that has input is invalid'
            ]
        );
        $this->form_validation->set_rules(
            'password',
            'password',
            'required|trim|min_length[8]',
            [
                'required'          => 'Password is required',
                'min_length'        => 'Minimum 8 character password'
            ]
        );
        if (isset($_SESSION['email'])) {
            if ($ref != '') {
                redirect($ref);
            } else {
                switch ($this->session->userdata('id_role')) {
                    case '1':
                        redirect('Admin/user');
                        break;
                        // case '2':
                        //     redirect('user');
                        //     break;
                    default:
                        // $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">
                        // Who are you as ?
                        //     </div>');

                        redirect('produksi/region/' . $this->session->userdata('menu_access')[0]);
                        break;
                }
            }
        } else if ($this->form_validation->run() == false) {
            $this->load->view('auth/header', $data);
            $this->load->view('auth/login');
            $this->load->view('auth/footer');
        } else {
            //validasi lolos
            $this->_cek_login($ref);
        }
    }

    private function _cek_login($ref = '')
    {
        $ref = !empty($ref) ? base64_decode($ref) : '';

        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $user = $this->db->get_where('tb_user', ['email' => $email])->row_array();

        //cek jika ada user
        if ($user) {
            //cek usernya aktiv
            if ($user['is_active'] == 1) {
                //cek password
                if (password_verify($password, $user['password'])) {
                    $access_menu = array_column($this->db->get_where('tb_access_menu', ['id_role' => $user['id_role']])->result_array(), 'id_menu');

                    if (!empty($access_menu)) {
                        $menu_category = $this->db->query("select * from tb_menu where id in (" . implode(',', $access_menu) . ")")->result_array();

                        $access_region = array_column($this->db->get_where('tb_access_region', ['id_user' => $user['id']])->result_array(), 'id_region');

                        if (!in_array(1, $access_menu)) {
                            if (!empty($access_region)) {
                                $this->db->select('*');
                                $this->db->from('tb_submenu');
                                $this->db->where('binding_id_region in (' . implode(', ', $access_region) . ') ');
                                $this->db->order_by('order_by', 'asc');
                                $list_submenu = $this->db->get()->result_array();
                            } else {
                                $list_submenu = [];
                            }
                        } else {
                            $this->db->select('*');
                            $this->db->from('tb_submenu');
                            $this->db->order_by('order_by', 'asc');
                            $list_submenu = $this->db->get()->result_array();
                        }

                        $list_menu = array_unique(array_column($list_submenu, 'id_menu_list'));
                        $this->db->select('*');
                        $this->db->from('tb_menu_list');
                        if (!empty($list_menu) && !empty($access_region)) {
                            $this->db->where('id in (' . implode(', ', $list_menu) . ') or binding_id_region in (' . implode(', ', $access_region) . ') or id_menu in (1, 4, 5)');
                        } else if (empty($list_menu)) {
                            $this->db->where('binding_id_region in (' . implode(', ', $access_region) . ') or id_menu in (1, 4, 5)');
                        } else if (empty($access_region)) {
                            $this->db->where('id in (' . implode(', ', $list_menu) . ') or id_menu in (1, 4, 5)');
                        } else {
                            $this->db->where('id_menu in (1, 4, 5)');
                        }
                        $list_menu = $this->db->get()->result_array();
                    } else {
                        $access_region = [];
                        $menu_category = [];
                        $list_menu = [];
                        $list_submenu = [];
                    }
                    $data = [
                        'email' => $user['email'],
                        'id_role' => $user['id_role'],
                        'menu_access' => $access_menu,
                        'region_access' => $access_region,
                        'menu_category' => $menu_category,
                        'menu_list' => $list_menu,
                        'submenu' => $list_submenu
                    ];
                    $this->session->set_userdata($data);

                    if ($ref != '') {
                        redirect($ref);
                    } else {
                        switch ($data['id_role']) {
                            case '1':
                                redirect('Admin/user');
                                break;
                                // case '2':
                                //     redirect('user');
                                //     break;
                            default:
                                // $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">
                                // Who are you as ?
                                //     </div>');

                                redirect('produksi/region/' . $access_region[0]);
                                break;
                        }
                    }
                } else {
                    $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">
                   Wrong password !
                   </div>');
                    redirect('auth');
                }
            } else {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">
                This Email has not activated !
               </div>');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">
           Email is not register !
          </div>');
            redirect('auth');
        }
    }

    public function register()
    {
        $data = [
            'judul'         => 'Register Page',
            'id_role'       => $this->db->get('tb_role')->result()
        ];

        $this->form_validation->set_rules(
            'username',
            'username',
            'required|trim',
            [
                'required'          => 'Username is required',
            ]
        );
        $this->form_validation->set_rules(
            'email',
            'email',
            'required|trim|valid_email|is_unique[tb_user.email]',
            [
                'required'          => 'Email is required',
                'valid_email'       => 'The email that has input is invalid',
                'is_unique'         => 'This email has already register !'
            ]
        );
        $this->form_validation->set_rules(
            'password',
            'password',
            'required|trim|min_length[8]|matches[password1]',
            [
                'required'          => 'Password is required',
                'min_length'        => 'Minimum 8 character password',
                'matches'           => 'Password dont match !'
            ]
        );
        $this->form_validation->set_rules(
            'password1',
            'password',
            'required|trim|matches[password]',
            [
                'required'          => 'Password is required',
            ]
        );
        $this->form_validation->set_rules(
            'no_hp',
            'no_hp',
            'required|trim|numeric',
            [
                'required'          => 'No Hp is required',
                'numeric'           => 'No Hp must be a Number'
            ]
        );

        if ($this->form_validation->run() == false) {
            $this->load->view('auth/header', $data);
            $this->load->view('auth/register');
            $this->load->view('auth/footer');
        } else {
            $this->Model_auth->tambahRegister();
            if (!$this->session->userdata('email')) {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">
                Congratilation ! your account has been created. Please login
              </div>');
                redirect('auth');
            } else {
                $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">
                Congratilation ! Add new data
              </div>');
                redirect('Admin/user');
            }
        }
    }

    public function hapusUser($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tb_user');

        $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">
        Congratilation ! Success Deleting Data
      </div>');
        redirect('Admin/user');
    }

    public function logout()
    {

        $this->session->unset_userdata('email');
        $this->session->unset_userdata('id_role');

        $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">
        You have been Logged out 
      </div>');
        redirect('auth');
    }

    public function blok()
    {
        redirect('notifikasi');
    }
}
