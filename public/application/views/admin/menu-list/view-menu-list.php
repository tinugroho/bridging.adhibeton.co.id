<!-- Page-body start -->
<div class="page-body">
    <!-- Basic table card start -->
    <div class="card">
        <div class="card-header">
            <?= $this->session->flashdata('pesan'); ?>
            <!-- button modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addMenuList">
                <i class="fas fa-plus mr-3"> Add Menu List</i>
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
                            <th>Nama Menu</th>
                            <th>Url</th>
                            <th>icons</th>
                            <th>Category Menu</th>
                            <th>Binding Region</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($menulist as $ml) : ?>
                            <tr>
                                <th scope="row"><?= $no++; ?></th>
                                <td><?= $ml['nama_menu']; ?></td>
                                <td><?= $ml['url']; ?></td>
                                <td><?= $ml['icon']; ?></td>
                                <td><?= $ml['menu']; ?></td>
                                <td><?= $ml['region_name']; ?></td>
                                <td>
                                    <a href="" class="text-success btn-modal-edit" data-toggle="modal" data-target="#editMenuList" data-id="<?= $ml['id']; ?>" data-nama="<?= $ml['nama_menu']; ?>" data-url="<?= $ml['url']; ?>" data-icon="<?= $ml['icon']; ?>" data-category="<?= $ml['id_menu']; ?>" data-region="<?= $ml['binding_id_region'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= base_url('Admin/menu/hapusmenulist/') . $ml['id']; ?>?>" class="text-danger" onclick="return confirm('yakin menghapus ?');"><i class="fas fa-trash-alt ml-2"></i></a>
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
?>


<!-- Modal -->
<div class="modal fade" id="addMenuList" tabindex="-1" aria-labelledby="addMenuListLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMenuListLabel"><?= $judul; ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="nama_menu">Nama menu</label>
                        <input type="text" class="form-control" id="nama_menu" name="nama_menu" required>
                    </div>
                    <div class="form-group">
                        <label for="url">Url</label>
                        <input type="text" class="form-control" id="url" name="url" required>
                    </div>
                    <div class="form-group">
                        <label for="icon">icon</label>
                        <input type="text" class="form-control" id="icon" name="icon" required>
                    </div>
                    <div class="form-group">
                        <label for="id_menu">Category Menu</label>
                        <select class="form-control" id="add_id_category" name="id_menu" required>
                            <option value="">Please select</option>
                            <?php foreach ($menu as $m) : ?>
                                <option value="<?= $m['id']; ?>"><?= $m['menu']; ?></option>
                            <?php endforeach; ?>
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- end modal add -->

<!-- modal edit menu list -->
<div class="modal fade" id="editMenuList" tabindex="-1" aria-labelledby="editMenuListLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMenuListLabel"><?= $judul; ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?= base_url('Admin/menu/editmenulist'); ?>">
                    <input type="hidden" id="edit_data_id" name="id" value="">
                    <div class="form-group">
                        <label for="nama_menu">Nama menu</label>
                        <input type="text" class="form-control" id="edit_nama_menu" name="nama_menu" value="" required>
                    </div>
                    <div class="form-group">
                        <label for="url">Url</label>
                        <input type="text" class="form-control" id="edit_url" name="url" value="" required>
                    </div>
                    <div class="form-group">
                        <label for="icon">icon</label>
                        <input type="text" class="form-control" id="edit_icon" name="icon" value="" required>
                    </div>
                    <div class="form-group">
                        <label for="id_menu">Category Menu</label>
                        <select class="form-control" id="edit_id_category" name="id_menu" required>
                            <?php foreach ($menu as $m) : ?>
                                <option value="<?= $m['id']; ?>"><?= $m['menu']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_binding_region">Binding Region</label>
                        <select class="form-control" id="edit_binding_region" name="id_region" required>
                            <option value="">Please select</option>
                            <?php foreach ($region as $rgn) : ?>
                                <option value="<?= $rgn['id_region']; ?>"><?= $rgn['region_name']; ?></option>
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
    var btnEdit = document.querySelectorAll(".btn-modal-edit");
    btnEdit.forEach((element, index) => {
        element.addEventListener('click', (e) => {
            edit_data_id.value = element.getAttribute('data-id');
            edit_nama_menu.value = element.getAttribute('data-nama');
            edit_url.value = element.getAttribute('data-url');
            edit_icon.value = element.getAttribute('data-icon');
            edit_id_category.value = element.getAttribute('data-category');
            if (element.getAttribute('data-category') == 2 || element.getAttribute('data-category') == 3) {
                edit_binding_region.value = element.getAttribute('data-region') == 0 ? '' : element.getAttribute('data-region');
                edit_binding_region.disabled = false;
            } else {
                edit_binding_region.value = '';
                edit_binding_region.disabled = true;
            }
        });
    });

    edit_id_category.addEventListener("change", function() {
        if (this.value == 2 || this.value == 3) {
            edit_binding_region.disabled = false;
        } else {
            edit_binding_region.value = '';
            edit_binding_region.disabled = true;
        }
    });
    add_id_category.addEventListener("change", function() {
        if (this.value == 2 || this.value == 3) {
            add_binding_region.disabled = false;
        } else {
            add_binding_region.value = '';
            add_binding_region.disabled = true;
        }
    });
</script>