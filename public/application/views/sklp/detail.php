<?php $arr_sklp = $data->result_array(); ?>

<!-- Page-body start -->
<div class="page-body">
    <!-- Basic table card start -->
    <div class="card">
        <div class="card-header">
            <?= $this->session->flashdata('pesan'); ?>
            <h5>SKLP Detail</h5>
            <hr>

            <div class="card-header-right">
                <ul class="list-unstyled card-option">
                    <li><i class="icofont icofont-simple-left "></i></li>
                    <li><i class="icofont icofont-maximize full-card"></i></li>
                    <li><i class="icofont icofont-minus minimize-card"></i></li>
                    <li><i class="icofont icofont-refresh reload-card"></i></li>
                    <!-- <li><i class="icofont icofont-error close-card"></i></li> -->
                </ul>
            </div>
            <div style="overflow-x:auto;">
                <table style="width:100%;">
                    <tr>
                        <th>Customer</th>
                        <td>:<?= !empty($arr_sklp) ? $arr_sklp[0]['Customer_Description'] : '' ?></td>
                        <th>Job Code</th>
                        <td>:<?= !empty($arr_sklp) ? $arr_sklp[0]['PO_Num'] : '' ?></td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>:<?= !empty($arr_sklp) ? $arr_sklp[0]['Address_Line1'] : '' ?></td>
                        <th>Jobmix</th>
                        <td>:<?= !empty($arr_sklp) ? $arr_sklp[0]['Jobmix'] : '' ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>:<?= !empty($arr_sklp) ? $arr_sklp[0]['Address_Line2'] : '' ?></td>
                        <th>Slump</th>
                        <td>:<?= !empty($arr_sklp) ? $arr_sklp[0]['Consistence'] : '' ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>:<?= !empty($arr_sklp) ? $arr_sklp[0]['Address_Line3'] : '' ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="card-block table-border-style mx-3 mb-4">
            <div class="table-responsive">
                <table id="tabel_history_sklp" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Ticket Code</th>
                            <th>Vol</th>
                            <th>V. Ordered</th>
                            <th>V. Cumulative</th>
                            <th>Truck</th>
                            <th>Driver</th>
                            <th>Referensi</th>
                            <th>Tgl Produksi</th>
                            <th>Tgl Post</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $no = 1;
                        foreach ($arr_sklp as $sklp) {
                        ?>
                            <tr>
                                <td><?= $no ?></td>
                                <td><?= $sklp['Ticket_Code'] ?></td>
                                <td><?= $sklp['Load_Size'] ?> m3</td>
                                <td><?= $sklp['Ordered_Qty'] ?> m3</td>
                                <td><?= $sklp['Delivered_Qty'] ?> m3</td>
                                <td><?= $sklp['Truck_Code'] ?></td>
                                <td><?= $sklp['Driver_Name'] ?></td>
                                <td><?= $sklp['index_load'] ?></td>
                                <td><?= str_replace(' ', '<br>', $sklp['RecordDate']) ?></td>
                                <td><?= str_replace(' ', '<br>', $sklp['tgl_post']) ?></td>
                                <td><?= $sklp['status'] ?></td>
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