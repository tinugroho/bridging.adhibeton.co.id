<!-- Page-body start -->
<div class="page-body">
    <!-- Basic table card start -->
    <div class="card">
        <div class="card-header">
            <?= $this->session->flashdata('pesan'); ?>
            <h5>API Active</h5> <?= !empty($data) ? '(Last Update : ' . date_format(date_create($data[0]->API_Date), 'Y-m-d H:i') . ')' : ''; ?>
            <hr>

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
                <table id="tabel_active" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Type</th>
                            <th>JO Number</th>
                            <th>Jobmix</th>
                            <th>Item QTY</th>
                            <th>Remaining QTY</th>
                            <th>JO Date</th>
                            <th>Post Sukses</th>
                            <th>Post Gagal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($data as $val) {
                        ?>
                            <tr>
                                <th><?= $no++; ?></th>
                                <td><?= $val->type ?></td>
                                <td><?= $val->JO_Number ?></td>
                                <td><?= $val->Item_Code ?></td>
                                <td><?= number_format(round($val->Item_Qty, 2), 2, ',', '.') ?></td>
                                <td><?= number_format(round($val->Remaining_Qty, 2), 2, ',', '.') ?></td>
                                <td><?= empty($val->JO_Date) ? '' : date_format(date_create($val->JO_Date), 'Y-m-d') ?></td>
                                <td>
                                    <?php
                                    if ($val->post_sukses > 0) {
                                        $form_sukses = '<form target="_blank" action="' . base_url('Api/sukses') . '" method="GET">
                                                            <input type="hidden" name="type" value="' . $val->type . '">
                                                            <input type="hidden" name="JO_Number" value="' . $val->JO_Number . '">
                                                            <button type="submit" class="btn btn-info badge">
                                                            ' . $val->post_sukses . '
                                                            </button>
                                                        </form>';
                                        echo $form_sukses;
                                    } else {
                                        echo $val->post_sukses;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($val->post_gagal > 0) {
                                        $form_sukses = '<form target="_blank" action="' . base_url('Api/gagal') . '" method="GET">
                                                            <input type="hidden" name="type" value="' . $val->type . '">
                                                            <input type="hidden" name="JO_Number" value="' . $val->JO_Number . '">
                                                            <button type="submit" class="btn btn-info badge">
                                                            ' . $val->post_gagal . '
                                                            </button>
                                                        </form>';
                                        echo $form_sukses;
                                    } else {
                                        echo $val->post_gagal;
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php  }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php //echo '<pre>' . (print_r(($data), true)) . '</pre>'
            ?>
        </div>
    </div>
</div>