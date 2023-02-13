<!-- Page-body start -->
<div class="page-body">
    <!-- Basic table card start -->
    <div class="card">
        <div class="card-header">
            <?= $this->session->flashdata('pesan'); ?>
            <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addSubMenu">
                <i class="fas fa-plus mr-3"> Add Sub Menu</i>
            </button> -->
            <a href="" data-toggle="modal" data-target="#addUser" class="btn btn-primary">Add new user</a>

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
        <script>
            var accessRegion=[];
        </script>
        <div class="card-block table-border-style">
            <div class="table-responsive">
                <table class="table table-striped table-bordered zero-configuration">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <!-- <th>Urutan</th> -->
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($users as $user) :
                            unset($user['password']);
                        ?>
                            <tr>
                                <th scope="row"><?= $no++; ?></th>
                                <td><?= $user['username']; ?></td>
                                <td><?= $user['email']; ?></td>
                                <td><?= $user['role']; ?></td>

                                <td>
                                    <?php if ($user['is_active'] == 1) : ?>
                                        <button class="badge badge-success">Aktif</button>
                                    <?php else : ?>
                                        <button class="badge badge-danger">verify</button>
                                    <?php endif; ?>
                                </td>

                                <!-- <td><?= $user['order_by']; ?></td> -->
                                <td>
                                    <a href="" class="text-success btn-edit-user" data-toggle="modal" data-target="#editUser" data-username="<?= $user['username'] ?>" data-email="<?= $user['email'] ?>" data-id="<?= $user['id']; ?>" data-id-role="<?= $user['id_role'] ?>"><i class="fas fa-edit"></i></a>
                                    <script>
                                        accessRegion["<?= $user['id']; ?>"]=<?=$user['access_region']?>;
                                    </script>
                                    <a href="<?= base_url('Auth/hapusUser/') . $user['id']; ?>" class="text-danger" onclick="return confirm('yakin menghapus ?');"><i class="fas fa-trash-alt ml-2"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- add user -->

<div class="modal fade" id="addUser" tabindex="-1" aria-labelledby="addUserLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserLabel">Add User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="addDataId" value="0">
                    <div class="form-group">
                        <label for="addDataUsername">Username</label>
                        <input type="text" name="username" class="form-control" id="addDataUsername" aria-describedby="usernameHelp" placeholder="Enter Username">
                    </div>
                    <div class="form-group">
                        <label for="addDataEmail">Email</label>
                        <input type="email" name="email" class="form-control" id="addDataEmail" aria-describedby="emailHelp" placeholder="Enter email">
                    </div>
                    <div class="form-group">
                        <label for="addDataIdRole">Role</label>
                        <select name="id_role" class="form-control" id="addDataIdRole">
                            <?php
                            foreach ($role as $val) {
                                echo '<option value="' . $val['id'] . '">' . $val['role'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <!-- <label for="">Access Regional</label>
                    <div class="form-group">
                    <?php
                    foreach($all_regional as $val){
                    ?>
                    <div class="form-check form-check-inline ml-0 mr-3">
                        <input class="form-check-input input_user_region ml-0" type="checkbox" id="user_region_<?=$val['id_region']?>" name="region[]" value="<?=$val['id_region']?>">
                        <label class="form-check-label" for="user_region_<?=$val['id_region']?>"><?=$val['region_name']?></label>
                    </div>
                    <?php
                    }
                    ?>
                    </div> -->
                    <div class="form-group">
                        <label for="addDataPassword">Password</label>
                        <input type="password" name="password" class="form-control" id="addDataPassword" aria-describedby="passwordHelp" placeholder="Enter Password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- edit user -->

<div class="modal fade" id="editUser" tabindex="-1" aria-labelledby="editUserLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserLabel">Update User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="editDataId" value="">
                    <div class="form-group">
                        <label for="editDataUsername">Username</label>
                        <input type="text" name="username" class="form-control" id="editDataUsername" aria-describedby="usernameHelp" placeholder="Enter Username">
                    </div>
                    <div class="form-group">
                        <label for="editDataEmail">Email</label>
                        <input type="email" name="email" class="form-control" id="editDataEmail" aria-describedby="emailHelp" placeholder="Enter email" readonly>
                    </div>
                    <div class="form-group">
                        <label for="editDataIdRole">Role</label>
                        <select name="id_role" class="form-control" id="editDataIdRole">
                            <?php
                            foreach ($role as $val) {
                                echo '<option value="' . $val['id'] . '">' . $val['role'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    
                    <label for="">Access Regional</label>
                    <div class="form-group">
                        <?php
                    foreach($all_regional as $val){
                    ?>
                    <div class="form-check form-check-inline ml-0 mr-3">
                        <input class="form-check-input input_user_region ml-0" type="checkbox" id="user_region_<?=$val['id_region']?>" name="region[]" value="<?=$val['id_region']?>">
                        <label class="form-check-label" for="user_region_<?=$val['id_region']?>"><?=$val['region_name']?></label>
                    </div>
                    <?php
                    }
                    ?>
                    </div>
                    <div class="form-group">
                        <label for="editDataPassword">New Password</label>
                        <input type="password" name="password" class="form-control" id="editDataPassword" aria-describedby="passwordHelp" placeholder="Enter Password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var btnEdit = document.querySelectorAll('.btn-edit-user');
    var input_user_region = document.querySelectorAll('.input_user_region');
    btnEdit.forEach((element, index) => {
        element.addEventListener('click', (e) => {
            editDataId.value = element.getAttribute('data-id');
            editDataUsername.value = element.getAttribute('data-username');
            editDataEmail.value = element.getAttribute('data-email');
            editDataIdRole.value = element.getAttribute('data-id-role');
            input_user_region.forEach((el_region, index) => {
                el_region.checked = false;
                if(accessRegion[element.getAttribute('data-id')].includes(el_region.value)){
                    el_region.checked = true;
                }
            });
        });
    });
</script>
