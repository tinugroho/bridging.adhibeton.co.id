<style>
    .table td {
        word-wrap: break-word;
        max-width: 100px;
        white-space: normal !important;
    }
</style>

<!-- <div class="container-fluid"> -->

<div class="page-body">
    <!-- Basic table card start -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4>Produksi <?= $judul . ' (' . ucfirst($mesin) . ')' ?></h4>

                <?= $tabs ?>

                <div class="row mt-5">
                    <div class="col-lg-10">
                        <form class="form-inline" method="GET" action="<?= base_url('produksi/region/') . $id_region . '/' . $mesin; ?>">
                            <div class="form-group">
                                <?php
                                if (isset($_GET['start'])) {
                                    $start = $this->input->get('start');
                                } else {
                                    // $start = date('Y-m-d H:i:s');

                                    $start = new DateTime("now", new DateTimeZone('Asia/Jakarta'));
                                    $start->modify('-7 day');
                                    $start = $start->format('Y-m-d H:i:s');
                                }
                                if (isset($_GET['end'])) {
                                    $end = $this->input->get('end');
                                } else {
                                    $end = new DateTime("now", new DateTimeZone('Asia/Jakarta'));
                                    $end = $end->format('Y-m-d H:i:s');
                                }
                                ?>
                                <label for="from">From</label>
                                <input type="text" id="start" name="start" class="form-control mx-sm-3 datetimepicker" value="<?= $start ?>">
                                <label for="to">To</label>
                                <input type="text" id="end" name="end" class="form-control mx-sm-3 datetimepicker" value="<?= $end ?>">
                                <select class="form-control mx-sm-3 my-2" name="BP_ID" id="BP_ID">
                                    <?= $select_bp ?>
                                </select>
                                <div class="mr-2">
                                    <div>
                                        <label for="jo" class="justify-content-start">
                                            <input class="mr-1" type="checkbox" id="jo" name="jo" value="jo" <?= isset($_GET['jo']) ? 'checked' : '' ?>>
                                            JO
                                        </label>
                                    </div>
                                    <div>
                                        <label for="sklp">
                                            <input class="mr-1" type="checkbox" id="sklp" name="sklp" value="sklp" <?= isset($_GET['sklp']) ? 'checked' : '' ?>>
                                            SKLP
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-outline-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>

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

            <div class="card-block table-border-style" style="font-size: small;">
                <div class="table-responsive">
                    <table id="tabel_produksi_cmd_batch" class="table table-striped table-bordered w-100" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">No Ticket</th>
                                <th rowspan="2">Referensi</th>
                                <th rowspan="2">No JO</th>
                                <th rowspan="2">SKLP</th>
                                <th rowspan="2">Jobmix</th>
                                <th rowspan="2">Load Qty</th>
                                <th rowspan="2">Customer</th>
                                <th rowspan="2">Ticket date</th>
                                <th colspan="2" class="text-center">Total</th>
                                <!-- <th rowspan="2">Akumulatif Vol</th> -->
                                <th rowspan="2">BP Name</th>
                                <th rowspan="2">Status</th>
                            </tr>
                            <tr>
                                <th>load</th>
                                <th>batch</th>
                            </tr>
                        </thead>

                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- </div> -->