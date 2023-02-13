<!-- Page-body start -->
<div class="page-body">
    <!-- Basic table card start -->
    <div class="card">
        <div class="card-header">
            <?= $this->session->flashdata('pesan'); ?>
            <h5>API Detail</h5>
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
                    <h5>Post Date</h5>
                </div>
                <div class="col-md-8">: <?= $Running_Date ?></div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <h5>Record Date</h5>
                </div>
                <div class="col-md-8">: <?= $RecordDate ?></div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <h5>Keterangan</h5>
                </div>
                <div class="col-md-8">: <?= $Keterangan ?></div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <h5>Index Load</h5>
                </div>
                <div class="col-md-7">: <?= $Index_Load ?></div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <h5 class="font-weight-bold">Load Size</h5>
                </div>
                <div class="col-md-8">: <?= !empty($Load_Size) ? $Load_Size . ' m3' : '' ?></div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <h5 class="font-weight-bold">Type</h5>
                </div>
                <div class="col-md-8">: <?= $type ?></div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <h5 class="font-weight-bold">Mesin</h5>
                </div>
                <div class="col-md-8">: <?= $mesin ?></div>
            </div>
        </div>
        <div class="card-block">
            <form method="POST">
                <div class="row mt-1">
                    <div class="col-md-7">
                        <div class="form-group">
                            <label for="JO_Number" class="font-weight-bold">No Jobmix</label>
                            <input type="text" name="JO_Number" class="form-control" id="JO_Number" value="<?= $JO_Number ?>" readonly>

                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Material</label>
                    <?php
                    if (!empty($Material)) {
                        $arrMaterial = explode('-', $Material);
                        foreach ($arrMaterial as $val) {
                            $per_material = explode('|', $val);
                    ?>
                            <div class="row mt-1">
                                <div class="col-md-7">
                                    <input type="text" class="form-control" name="Material[]" value="<?= $per_material[0] ?>" readonly>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" value="<?= $per_material[1] ?>" readonly>
                                </div>
                            </div>
                    <?php }
                    } ?>
                </div>

            </form>
        </div>
    </div>
</div>