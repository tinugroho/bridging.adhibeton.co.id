<!-- Page-body start -->
<div class="page-body">
    <!-- Basic table card start -->
    <div class="card">
        <div class="card-header">
            <?= $this->session->flashdata('pesan'); ?>
            <h5>API History</h5>
            <hr>

            <div class="row mt-5">
                <div class="col-lg-10">
                    <form class="form-inline" method="GET" action="<?= base_url('Api/history'); ?>">
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
        <div class="card-block table-border-style">
            <div class="table-responsive">
                <table id="tabel_history" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>JO Number</th>
                            <th>Ticket Code</th>
                            <th>Jobmix ERP</th>
                            <th>Jobmix BP</th>
                            <th>Tgl Produksi</th>
                            <th>Tgl Post</th>
                            <th>Ref</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <?php //echo '<pre>' . (print_r(($data), true)) . '</pre>'
            ?>
        </div>
    </div>
</div>