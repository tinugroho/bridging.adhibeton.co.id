<!-- Page-body start -->
<div class="page-body">
    <!-- Basic table card start -->
    <div class="card">
        <div class="card-header">
            <?= $this->session->flashdata('pesan'); ?>
            <h4>Logs API Sukses</h4>

            <div class="card-header-right">
                <ul class="list-unstyled card-option">
                    <li><i class="icofont icofont-simple-left "></i></li>
                    <li><i class="icofont icofont-maximize full-card"></i></li>
                    <li><i class="icofont icofont-minus minimize-card"></i></li>
                    <li><i class="icofont icofont-refresh reload-card"></i></li>
                    <li><i class="icofont icofont-error close-card"></i></li>
                </ul>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-2">
                    <h5>JO Number</h5>
                </div>
                <div class="col-md-8">: <?= $JO_Number ?></div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <h5>Type</h5>
                </div>
                <div class="col-md-8">: <?= $type ?></div>
            </div>
        </div>
        <div class="card-block table-border-style">
            <div class="table-responsive">
                <table class="table table-striped table-bordered zero-configuration">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jomix ERP</th>
                            <th>Jomix BP</th>
                            <th>Material</th>
                            <th>Load Size</th>
                            <th>Tgl Produksi</th>
                            <th>Tgl Post</th>
                            <th>Ref</th>
                            <th>Region/Mesin</th>
                            <th>Status</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($data as $val) {
                        ?>
                            <tr>
                                <th><?= $no++; ?></th>
                                <td><?= $val->Jobmix_ERP ?></td>
                                <td style="white-space: break-spaces;"><?= $val->Jobmix_BP ?></td>
                                <td>
                                    <?php
                                    $arrMaterial = explode("-", $val->Material);
                                    foreach ($arrMaterial as $material) {
                                        echo $material . '<br>';
                                    }
                                    ?>
                                </td>
                                <td><?= $val->Post_Qty ?></td>
                                <td><?= date_format(date_create($val->CreateDateBP), 'Y-m-d H:i') ?></td>
                                <td><?= date_format(date_create($val->Method_Date), 'Y-m-d H:i') ?></td>
                                <td><?= $val->Index_Load ?></td>
                                <td><?= $val->region_name . '<br>' . $val->db ?></td>
                                <td><?= $val->Keterangan ?></td>
                                <td>
                                    <form target="_blank" action="<?= base_url('Api/detail') ?>" method="GET">
                                        <input type="hidden" name="type" value="<?= $val->type ?>">
                                        <input type="hidden" name="Index_Log" value="<?= $val->Index_Log ?>">
                                        <input type="hidden" name="mesin" value="<?= $val->db ?>">
                                        <button type="submit" class="btn btn-info badge">
                                            Detail
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>