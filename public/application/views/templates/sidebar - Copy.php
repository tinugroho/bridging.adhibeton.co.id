<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <nav class="pcoded-navbar">
            <div class="sidebar_toggle"><a href="#"><i class="icon-close icons"></i></a></div>
            <div class="pcoded-inner-navbar main-menu">
                <!-- <div class="">
                    <div class="main-menu-header">
                        <a href="">
                            <img class="img-40 img-radius" src="<?= base_url('assets/img/') . $user['image']; ?>" alt="User-Profile-Image">
                        </a>

                        <div class="user-details">
                            <span><?= $user['username']; ?></span>
                            <span id="more-details"><?= $user['role']; ?><i class="ti-angle-down"></i></span>
                        </div>
                    </div>

                    <div class="main-menu-content">
                        <ul>
                            <li class="more-details">
                                <a href="" data-toggle="modal" data-target="#viewProfile"><i class="ti-user"></i>View Profile</a>
                                <a href="" data-toggle="modal" data-target="#setting"><i class="ti-settings"></i>Settings</a>
                                <a href="" data-toggle="modal" data-target="#gantiPassword"><i class="ti-unlock"></i>Change Password</a>
                                <a href="<?= base_url('auth/logout'); ?>"><i class="ti-layout-sidebar-left"></i>Logout</a>
                            </li>
                        </ul>
                    </div>
                </div> -->
                <!-- <div class="pcoded-search">
                    <span class="searchbar-toggle"> </span>
                    <div class="pcoded-search-box ">
                        <input type="text" placeholder="Search">
                        <span class="search-icon"><i class="ti-search" aria-hidden="true"></i></span>
                    </div>
                </div> -->

                <!-- menu dinamis -->
                <?php
                $role = $this->session->userdata('id_role');
                $qureymenu = "SELECT `tb_menu`.`id`, `menu`
                                FROM `tb_menu` JOIN `tb_access_menu`
                                  ON `tb_menu`.`id` = `tb_access_menu`.`id_menu`
                               WHERE `tb_access_menu`.`id_role` = $role
                            ORDER BY `tb_access_menu`.`id_menu` ASC
                             ";

                $menu = $this->db->query($qureymenu)->result_array();
                ?>

                <?php foreach ($menu as $m) : ?>
                    <div class="pcoded-navigatio-lavel" data-i18n="nav.category.navigation"><?= $m['menu']; ?></div>

                    <!-- query menu list -->
                    <?php
                    $menu_id = $m['id'];
                    $id_menu = $m['id'];
                    $this->db->select('tb_menu.*, tb_menu_list.*');
                    $this->db->from('tb_menu_list');
                    $this->db->join('tb_menu', 'tb_menu.id = tb_menu_list.id_menu');
                    $this->db->where('tb_menu_list.id_menu', $id_menu);
                    $menulist = $this->db->get()->result_array();
                    ?>

                    <!-- looping menu list and cek apakah menu list mempunyai submenu -->
                    <ul class="pcoded-item pcoded-left-item">
                        <?php foreach ($menulist as $ml) : ?>
                            <?php if ($ml['url'] != '#') : ?>
                                <?php if ($judul == $ml['nama_menu']) : ?>
                                    <li class="active">
                                    <?php else : ?>
                                    <li class=" ">
                                    <?php endif; ?>
                                    <a href="<?= base_url($ml['url']); ?>">
                                        <span class="pcoded-micon"><i class="<?= $ml['icon']; ?>"></i><b>D</b></span>
                                        <span class="pcoded-mtext" data-i18n="nav.dash.main"><?= $ml['nama_menu']; ?></span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                    </li>
                                <?php else : ?>
                                    <li class="pcoded-hasmenu">
                                        <a href="<?= $ml['url']; ?>">
                                            <span class="pcoded-micon"><i class="<?= $ml['icon']; ?>"></i></span>
                                            <span class="pcoded-mtext" data-i18n="nav.basic-components.main"><?= $ml['nama_menu']; ?></span>
                                            <span class="pcoded-mcaret"></span>
                                        </a>
                                        <ul class="pcoded-submenu">
                                            <!-- query submenu dari menu list -->
                                            <?php
                                            $id_menu_list = $ml['id'];
                                            $this->db->select('tb_menu_list.*, tb_submenu.*');
                                            $this->db->from('tb_submenu');
                                            $this->db->join('tb_menu_list', 'tb_menu_list.id = tb_submenu.id_menu_list');
                                            $this->db->where('tb_submenu.id_menu_list', $id_menu_list);
                                            $this->db->order_by('tb_submenu.order_by', 'asc');
                                            $submenu = $this->db->get()->result_array();
                                            ?>
                                            <!-- lopping submenu -->
                                            <?php foreach ($submenu as $sm) : ?>
                                                <?php if ($judul == $sm['submenu']) : ?>
                                                    <li class="active">
                                                    <?php else : ?>
                                                    <li class=" ">
                                                    <?php endif; ?>
                                                    <a href="<?= base_url($sm['url_sub']); ?>">
                                                        <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                                        <span class="pcoded-mtext" data-i18n="nav.basic-components.alert"><?= $sm['submenu']; ?></span>
                                                        <span class="pcoded-mcaret"></span>
                                                    </a>
                                                    </li>
                                                <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                    </ul>
            </div>
        </nav>

        <!-- setting -->
        <div class="modal fade" id="gantiPassword" tabindex="-1" role="dialog" aria-labelledby="gantiPasswordLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="gantiPasswordLabel">Change Password</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php echo form_open_multipart('profile/edit'); ?>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <input type="password" class="form-control" name="passwordLama" id="passwordLama" placeholder="input old password">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <input type="password" class="form-control" name="passwordBaru" id="passwordBaru" placeholder="input new password">
                            </div>
                        </div>


                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- setting -->
        <div class="modal fade" id="setting" tabindex="-1" role="dialog" aria-labelledby="settingLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="settingLabel">Setting</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php echo form_open_multipart('profile/edit'); ?>
                        <div class="form-group row">
                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" name="email" id="email" value="<?= $user['email']; ?>" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="username" class="col-sm-2 col-form-label">Username</label>
                            <div class="col-sm-10">
                                <input type="username" class="form-control" id="username" name="username" value="<?= $user['username']; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="image" class="col-sm-2 col-form-label">Image</label>
                            <div class="col-sm-10">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <img src="<?= base_url('assets/img/') . $user['image']; ?>" class="img-thumbnail">
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="image" name="image">
                                            <label class="custom-file-label" for="image">Choose file</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- modal view profile -->
        <div class="modal fade" id="viewProfile" tabindex="-1" role="dialog" aria-labelledby="viewProfileLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewProfileLabel">Your Profile</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="task-contain">
                                            <?php if ($user['id_role'] == '1') : ?>
                                                <img src="<?= base_url('assets/img/admin.jpg'); ?>" data-toggle="tooltip" title="<?= $user['username']; ?>" class="img-40" alt="">
                                            <?php else : ?>
                                                <img src="<?= base_url('assets/img/manager.jpg'); ?>" data-toggle="tooltip" title="<?= $user['username']; ?>" class="img-40" alt="">
                                            <?php endif; ?>
                                            <p class="d-inline-block m-l-20"><?= $user['role']; ?></p><br>
                                        </div>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="task-contain">
                                            <img src="<?= base_url('assets/img/username.jpg'); ?>" data-toggle="tooltip" title="<?= $user['username']; ?>" class="img-40" alt="">
                                            <p class="d-inline-block m-l-20"><?= $user['username']; ?></p>

                                        </div>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="task-contain">
                                            <img src="<?= base_url('assets/img/email.png'); ?>" data-toggle="tooltip" title="<?= $user['email']; ?>" class="img-40" alt="">
                                            <p class="d-inline-block m-l-20"><?= $user['email']; ?></p>
                                        </div>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="task-contain">
                                            <img src="<?= base_url('assets/img/tanggal.png'); ?>" data-toggle="tooltip" title="<?= date('d F Y', $user['date_created']); ?>" class="img-40" alt="">
                                            <p class="d-inline-block m-l-20"><?= date('d F Y', $user['date_created']); ?></p>
                                        </div>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                        </table>
                        <!-- </div>
                                    </div>
                                </div>
                            </div> -->

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                    </div>

                </div>
            </div>
        </div>


        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <div class="main-body">
                    <!-- <div class="page-wrapper"> -->