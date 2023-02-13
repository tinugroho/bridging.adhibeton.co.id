<!-- Page-body start -->
<div class="page-body">
    <!-- Basic table card start -->
    <div class="card">
        <div class="card-header">
            <?= $this->session->flashdata('pesan'); ?>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addSubMenu">
                <i class="fas fa-plus mr-3"> Add Sub Menu</i>
            </button>

            <div class="card-header-right">
                <ul class="list-unstyled card-option">
                    <li><i class="icofont icofont-simple-left "></i></li>
                    <li><i class="icofont icofont-maximize full-card"></i></li>
                    <li><i class="icofont icofont-minus minimize-card"></i></li>
                    <li><i class="icofont icofont-refresh reload-card"></i></li>
                    <li><i class="icofont icofont-error close-card"></i></li>
                </ul>
            </div>
        </div>
        <div class="card-block table-border-style">
            <div class="table-responsive">
                <table class="table zero-configuration">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Sub Menu</th>
                            <th>Url</th>
                            <th>Category Menu</th>
                            <th>Parent Menu</th>
                            <th>Binding Region</th>
                            <th>Urutan</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($submenu as $sm) : ?>
                            <tr>
                                <th scope="row"><?= $no++; ?></th>
                                <td><?= $sm['submenu']; ?></td>
                                <td><?= $sm['url_sub']; ?></td>
                                <td><?= $sm['menu']; ?></td>
                                <td><?= $sm['nama_menu']; ?></td>
                                <td><?= $sm['region_name']; ?></td>
                                <td><?= $sm['order_by']; ?></td>
                                <td>
                                    <a href="" class="text-success btn-edit" data-toggle="modal" data-target="#editSubMenu" data-id="<?= $sm['id']; ?>" data-category="<?= $sm['category_id']; ?>" data-submenu="<?= $sm['submenu']; ?>" data-url="<?= $sm['url_sub']; ?>" data-parent="<?= $sm['id_menu_list']; ?>" data-region="<?= $sm['binding_id_region']; ?>" data-urutan="<?= $sm['order_by']; ?>"><i class="fas fa-edit"></i></a>
                                    <a href="<?= base_url('Admin/menu/hapussubmenu/') . $sm['id']; ?>" class="text-danger" onclick="return confirm('yakin menghapus ?');"><i class="fas fa-trash-alt ml-2"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<?php
$this->db->select('tb_menu_list.*, tb_menu.menu');
$this->db->from('tb_menu_list');
$this->db->join('tb_menu', 'tb_menu_list.id_menu=tb_menu.id', 'left');
$this->db->where("tb_menu_list.url = '#'");
$this->db->order_by('tb_menu.id', 'asc');
$query = $this->db->get();
$menu_list = $query->result_array();
$order_by = ['1', '2', '3', '4', '5', '6'];
// echo '<pre>';
// print_r($menu_list);
// echo '</pre>';
?>


<!-- Modal -->
<div class="modal fade" id="addSubMenu" tabindex="-1" aria-labelledby="addSubMenuLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSubMenuLabel">Add Submenu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="submenu">Nama submenu</label>
                        <input type="text" class="form-control" id="submenu" name="submenu" required>
                    </div>
                    <div class="form-group">
                        <label for="url_sub">Url</label>
                        <input type="text" class="form-control" id="url_sub" name="url_sub" required>
                    </div>
                    <div class="form-group">
                        <label for="id_menu_list">Parent Menu</label>
                        <select class="form-control" id="id_menu_list" name="id_menu_list" required>
                            <option value="">-- Please Select --</option>

                            <?php
                            $first = true;
                            $no_submenu = 0;
                            foreach ($menu_list as $ml) :
                                // if (array_count_values(array_column($menu_list, 'id_menu'))[$ml['id_menu']] > 1) {
                                $no_submenu++;
                                if ($first) {
                                    $first = false;
                                    echo '<optgroup label="' . $ml['menu'] . '">';
                                }
                                // }
                            ?>
                                <option value="<?= $ml['id']; ?>" data-category="<?= $ml['id_menu']; ?>"><?= $ml['menu'] . ' - ' . $ml['nama_menu']; ?></option>
                            <?php
                                if ($no_submenu == array_count_values(array_column($menu_list, 'id_menu'))[$ml['id_menu']]) {
                                    echo '</optgroup>';
                                    $first = true;
                                    $no_submenu = 0;
                                }
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="add_binding_region">Binding Region</label>
                        <select class="form-control" id="add_binding_region" name="id_region" disabled required>
                            <option value="">Please select</option>
                            <?php foreach ($region as $rgn) : ?>
                                <option value="<?= $rgn['id_region']; ?>"><?= $rgn['region_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="order_by">Urutan menu</label>
                        <select class="form-control" id="order_by" name="order_by">
                            <option>Please select</option>
                            <?php foreach ($order_by as $ob) : ?>
                                <option value="<?= $ob; ?>"><?= $ob; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- sub menu edit -->

<div class="modal fade" id="editSubMenu" tabindex="-1" aria-labelledby="editSubMenuLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSubMenuLabel">Update Submenu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?= base_url('Admin/menu/editsubmenu/') ?>">
                    <input type="hidden" id="edit_id" name="id" value="">
                    <div class="form-group">
                        <label for="submenu">Nama submenu</label>
                        <input type="text" class="form-control" id="edit_submenu" name="submenu" value="">
                    </div>
                    <div class="form-group">
                        <label for="url_sub">Url</label>
                        <input type="text" class="form-control" id="edit_url_sub" name="url_sub" value="">
                    </div>
                    <div class="form-group">
                        <label for="edit_id_menu_list">Action Menu List</label>
                        <select class="form-control" id="edit_id_menu_list" name="id_menu_list">
                            <option value="">-- Please Select --</option>
                            <?php
                            $first = true;
                            $no_submenu = 0;
                            foreach ($menu_list as $ml) :
                                // if (array_count_values(array_column($menu_list, 'id_menu'))[$ml['id_menu']] > 1) {
                                $no_submenu++;
                                if ($first) {
                                    $first = false;
                                    echo '<optgroup label="' . $ml['menu'] . '">';
                                }
                                // }
                            ?>
                                <option value="<?= $ml['id']; ?>" data-category="<?= $ml['id_menu']; ?>"><?= $ml['menu'] . ' - ' . $ml['nama_menu']; ?></option>
                            <?php
                                if ($no_submenu == array_count_values(array_column($menu_list, 'id_menu'))[$ml['id_menu']]) {
                                    echo '</optgroup>';
                                    $first = true;
                                    $no_submenu = 0;
                                }
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_binding_region">Binding Region</label>
                        <select class="form-control" id="edit_binding_region" name="id_region" disabled required>
                            <option value="">Please select</option>
                            <?php foreach ($region as $rgn) : ?>
                                <option value="<?= $rgn['id_region']; ?>"><?= $rgn['region_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="order_by">Urutan menu</label>
                        <select class="form-control" id="edit_order_by" name="order_by">
                            <?php foreach ($order_by as $ob) : ?>
                                <?php if ($ob == $sm['order_by']) : ?>
                                    <option value="<?= $ob; ?>" selected><?= $ob; ?></option>
                                <?php else : ?>
                                    <option value="<?= $ob; ?>"><?= $ob; ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    var id_menu_list = document.getElementById('id_menu_list');
    id_menu_list.addEventListener("change", function() {
        var selected = this.options[this.selectedIndex];
        var category = selected.getAttribute('data-category');
        if (category == 2 || category == 3) {
            add_binding_region.disabled = false;
        } else {
            add_binding_region.value = '';
            add_binding_region.disabled = true;
        }
    });

    var btn_edit = document.querySelectorAll('.btn-edit');
    btn_edit.forEach((element, index) => {
        element.addEventListener('click', (e) => {
            edit_id.value = element.getAttribute('data-id');
            edit_submenu.value = element.getAttribute('data-submenu');
            edit_url_sub.value = element.getAttribute('data-url');
            edit_id_menu_list.value = element.getAttribute('data-parent');

            if (element.getAttribute('data-category') == 2 || element.getAttribute('data-category') == 3) {
                edit_binding_region.value = element.getAttribute('data-region') == 0 ? '' : element.getAttribute('data-region');
                edit_binding_region.disabled = false;
            } else {
                edit_binding_region.value = '';
                edit_binding_region.disabled = true;
            }

            edit_order_by.value = element.getAttribute('data-urutan');
        });
    });

    edit_id_menu_list.addEventListener("change", function() {
        var selected = this.options[this.selectedIndex];
        var category = selected.getAttribute('data-category');
        if (category == 2 || category == 3) {
            edit_binding_region.disabled = false;
        } else {
            edit_binding_region.value = '';
            edit_binding_region.disabled = true;
        }
    });
</script>