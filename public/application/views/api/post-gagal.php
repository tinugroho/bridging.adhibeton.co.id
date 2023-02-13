<!-- Page-body start -->
<div class="page-body">
    <!-- Basic table card start -->
    <div class="card">
        <div class="card-header">
            <?= $this->session->flashdata('pesan'); ?>
            <h4>Logs API Gagal</h4>

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
                            <th>Jomix ERP/BP</th>
                            <th>Material</th>
                            <th>Load Size</th>
                            <th>Tgl Produksi/Post</th>
                            <th>Ref</th>
                            <th>Region/Mesin/BP</th>
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
                                <td>
                                    <b>Jobmix ERP:</b><br>
                                    <p><?= $val->Jobmix_ERP ?></p>
                                    <b>Jobmix BP:</b><br>
                                    <?= $val->Jobmix_BP ?>
                                </td>
                                <td>
                                    <?php
                                    $arrMaterial = explode("-", $val->Material);
                                    foreach ($arrMaterial as $material) {
                                        echo $material . '<br>';
                                    }
                                    ?>
                                </td>
                                <td><?= $val->Load_Size ?></td>
                                <td>
                                    <b>Tanggal Produksi:</b><br>
                                    <p><?= empty($val->RecordDate) ? '' : date_format(date_create($val->RecordDate), 'Y-m-d H:i')
                                        ?></p>
                                    <b>Tanggal Post:</b><br>
                                    <?= date_format(date_create($val->Running_Date), 'Y-m-d H:i') ?>
                                </td>
                                <td>
                                    <b>Index Load:</b><br>
                                    <p><?= $val->Index_Load ?></p>
                                    <b>No Tiket:</b><br>
                                    <?= $val->Ticket_Id ?>
                                </td>
                                <td><?= $val->region_name . '<br>' . $val->db . '<br>' . $val->bp_name ?></td>
                                <td style="white-space:normal;"><?= $val->Keterangan ?></td>
                                <td>

                                    <form target="_blank" action="<?= base_url('Api/repost') ?>" method="GET">
                                        <input type="hidden" name="type" value="<?= $val->type ?>">
                                        <input type="hidden" name="Index_Post_Gagal" value="<?= $val->Index_Post_Gagal ?>">
                                        <input type="hidden" name="mesin" value="<?= $val->db ?>">
                                        <button type="submit" class="btn btn-info badge">
                                            <?= $val->Keterangan != 'POSTED' ? 'Repost' : 'Detail' ?>
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