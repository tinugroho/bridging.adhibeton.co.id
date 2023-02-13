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
                  <table class="table">
                      <thead>
                          <tr>
                              <th>No</th>
                              <th>Nama Menu</th>
                              <th>Url</th>
                              <th>icons</th>
                              <th>Action menu</th>
                              <th>Action</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php
                            $no = 1;
                            // foreach ($menulist as $ml) : 
                            ?>
                          <tr>
                              <th scope="row"><?= $no++; ?></th>
                              <td>fsf</td>
                              <td>daas</td>
                              <td>dsasdsa</td>
                              <td>sdsdsa</td>
                              <td>
                                  <a href="" class="text-success" data-toggle="modal" data-target="#editMenuList"><i class="fas fa-edit"></i></a>
                                  <a href="<?= base_url('admin/menu/hapusmenulist/') ?>" class="text-danger" onclick="return confirm('yakin menghapus ?');"><i class="fas fa-trash-alt ml-2"></i></a>
                              </td>
                          </tr>

                      </tbody>
                  </table>
              </div>
          </div>
      </div>
  </div>