  <!-- Page-body start -->
  <div class="page-body">
      <!-- Basic table card start -->
      <div class="col-md-12">
          <div class="card">
              <div class="card-header">
                  <?= $this->session->flashdata('pesan'); ?>
                  <a href="" class="btn btn-primary" data-toggle="modal" data-target="#addRole"><i class="fas fa-plus mr-3"> Add Role</i></a>
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
                                  <th width="50">No</th>
                                  <th>Role</th>
                                  <th class="text-right">Action</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php
                                $no = 1;
                                foreach ($role as $r) : ?>
                                  <tr>
                                      <th scope="row"><?= $no++; ?></th>
                                      <td><?= $r['role']; ?></td>
                                      <td class="text-right">
                                          <a href="" class="text-warning" data-toggle="modal" data-target="#accessRole<?= $r['id']; ?>"><i class="fas fa-user-cog"></i></a>
                                          <a href="" class="text-success" data-toggle="modal" data-target="#editRole<?= $r['id']; ?>"><i class="fas fa-edit ml-2"></i></a>
                                          <a href="<?= base_url('Admin/menu/hapusrole/') . $r['id']; ?>" class="text-danger" onclick="return confirm('yakin menghapus ?');"><i class="fas fa-trash-alt ml-2"></i></a>
                                      </td>
                                  </tr>
                              <?php endforeach; ?>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <!-- Modal add -->
  <div class="modal fade" id="addRole" tabindex="-1" role="dialog" aria-labelledby="addRoleLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="addRoleLabel">Add Role</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <form method="post" action="">
                  <div class="modal-body">
                      <div class="form-group">
                          <label for="role">Role</label>
                          <input type="text" class="form-control" id="role" name="role">
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
  <!-- end modal add -->

  <!-- modal edit -->
  <?php
    $no = 1;
    foreach ($role as $r) : $no++; ?>
      <div class="modal fade" id="editRole<?= $r['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editRoleLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="editRoleLabel">Edit Role</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <form method="post" action="<?= base_url('Admin/menu/editrole/') . $r['id']; ?>">
                      <div class="modal-body">
                          <input type="hidden" name="id" value="<?= $r['id']; ?>">
                          <div class="form-group">
                              <label for="role">Role</label>
                              <input type="text" class="form-control" id="role" name="role" value="<?= $r['role']; ?>">
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
  <?php endforeach; ?>
  <!-- end modal edit -->

  <!-- access role menu -->
  <?php
    $no = 1;
    foreach ($role as $r) : $no++; ?>
      <div class="modal fade" id="accessRole<?= $r['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="accessRoleLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="accessRoleLabel"><?= $r['role']; ?></h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>

                  <!-- <div class="modal-body"> -->
                  <div class="container">
                      <div class="card-block table-border-style">
                          <div class="table-responsive">
                              <table class="table">
                                  <thead>
                                      <tr>
                                          <th>No</th>
                                          <th>Menu</th>
                                          <th>Access</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <?php
                                        $no = 1;
                                        foreach ($menu as $m) : ?>
                                          <tr>
                                              <th scope="row"><?= $no++; ?></th>
                                              <td><?= $m['menu']; ?></td>
                                              <td>
                                                  <div class="form-check ml-4">
                                                      <input class="form-check-input" type="checkbox" <?= check_access($r['id'], $m['id']); ?> data-role="<?= $r['id']; ?>" data-menu="<?= $m['id']; ?>">
                                                  </div>
                                              </td>
                                          </tr>
                                      <?php endforeach; ?>
                                  </tbody>
                              </table>
                          </div>
                      </div>
                  </div>
                  <!-- </div> -->
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      <!-- <button type="submit" class="btn btn-primary">Save changes</button> -->
                  </div>

              </div>
          </div>
      </div>
  <?php endforeach; ?>