<!-- Page-body start -->
<div class="page-body">
    <!-- Basic table card start -->
    <div class="card">
        <div class="card-header">
            <?= $this->session->flashdata('pesan'); ?>
            <h5>SKLP History</h5>
            <hr>
            <div class="row mt-5">
                <div class="col-lg-10">
                    <form class="form-inline" method="GET" action="<?= base_url('Sklp/history'); ?>">
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
                    <!-- <li><i class="icofont icofont-error close-card"></i></li> -->
                </ul>
            </div>
        </div>
        <div class="card-block table-border-style mx-3 mb-4">
            <div class="table-responsive">
                <table id="tabel_history_sklp" class="table table-striped table-bordered zero-configuration">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Job Code</th>
                            <th>Task</th>
                            <th>Jobmix</th>
                            <th>Slump</th>
                            <th>Total Load</th>
                            <th>V. Cumulative</th>
                            <th>Last Post</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $no = 1;
                        foreach ($data->result_array() as $sklp) {
                        ?>
                            <tr>
                                <td><?= $no ?></td>
                                <td><?= $sklp['PO_Num'] ?></td>
                                <td style="word-wrap: break-word"><?= $sklp['Project_Description'] ?></td>
                                <td><?= $sklp['Jobmix'] ?></td>
                                <td><?= $sklp['Consistence'] ?></td>
                                <td><?= $sklp['x_load'] ?></td>
                                <td><?= $sklp['cummulative'] ?> m3</td>
                                <td><?= str_replace(' ', '<br>', $sklp['last_post']) ?></td>
                                <td>
                                    <form method="get" action="<?= base_url('sklp/detail/') ?>" target="_blank">
                                        <input type="hidden" name="start" value="<?= $this->input->get('start') ?>">
                                        <input type="hidden" name="end" value="<?= $this->input->get('end') ?>">
                                        <input type="hidden" name="PO_Num" value="<?= $sklp['PO_Num'] ?>">
                                        <input type="hidden" name="Jobmix" value="<?= $sklp['Jobmix'] ?>">
                                        <input type="hidden" name="Consistence" value="<?= $sklp['Consistence'] ?>">
                                        <button type="submit" class="btn btn-info badge">Detail</button>
                                    </form>
                                </td>
                            </tr>
                        <?php
                            $no++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php //echo '<pre>' . (print_r(($data), true)) . '</pre>'
            ?>
        </div>
    </div>
</div>