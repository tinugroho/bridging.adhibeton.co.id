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
                <?= $this->session->flashdata('pesan'); ?>
                <!-- <a href="" class="btn btn-primary" data-toggle="modal" data-target="#addRole"><i class="fas fa-plus mr-3"> Add</i></a> -->
                <h4><?= $judul; ?></h4>



                <div class="row mt-5">
                    <div class="col-lg-10">
                        <form class="form-inline" method="GET" action="<?= base_url('ProduksiNew/region/') . $id_region; ?>">
                            <div class="form-group">
                                <label for="from">From</label>
                                <input type="text" id="start" name="start" class="form-control mx-sm-3 datetimepicker" value="<?= $this->input->get('start') ?>">
                                <label for="to">To</label>
                                <input type="text" id="end" name="end" class="form-control mx-sm-3 datetimepicker" value="<?= $this->input->get('end') ?>">
                                <select class="form-control mx-sm-3" name="BP_ID" id="BP_ID">
                                    <?= $select_bp ?>
                                </select>
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
                    <table id="tabel_produksi" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">No Ticket</th>
                                <th rowspan="2">No JO</th>
                                <th rowspan="2">Jobmix</th>
                                <th rowspan="2">Req Vol</th>
                                <th rowspan="2">Customer</th>
                                <th rowspan="2">Ticket date</th>
                                <th colspan="2" class="text-center">Total</th>
                                <th rowspan="2">Akumulatif Vol</th>
                                <th rowspan="2">BP Name</th>
                                <th rowspan="2">Status</th>
                            </tr>
                            <tr>
                                <th>load</th>
                                <th>bath</th>
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