<div class="page-body">
    <!-- Basic table card start -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <?= $this->session->flashdata('pesan'); ?>
                <!-- <a href="" class="btn btn-primary" data-toggle="modal" data-target="#addRole"><i class="fas fa-plus mr-3"> Add</i></a> -->
                <p>No JO : <?= reset($data)->Delivery_Instruction ?></p>
                <p>JMF : <?= reset($data)->Item_Code ?></p>
                <p>Customer : <?= reset($data)->Customer_Description ?></p>
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
            <!-- <h2 class="text-center" style="font-size: small;">load</h2> -->
            <div class="card-block table-border-style" style="font-size: x-small;">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered zero-configuration">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Tiket</th>
                                <th>No Load</th>
                                <th>Material Usage<br>Material/Target/Actual/Percent</th>
                                <th>Vol</th>
                                <!-- <th>req_vol</th> -->
                                <th>Akumulasi</th>
                                <th>Operator</th>
                                <th>Driver</th>
                                <th>Truck</th>
                                <th>Date</th>
                                <th>BP Name</th>
                                <th>Status</th>
                                <!-- <th>status</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            foreach ($data as $val) {
                                $no++;
                            ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td><?= $val->Ticket_Code ?></td>
                                    <td><?= $val->Order_Number_Of_Tickets ?></td>
                                    <td>
                                        <?php
                                        foreach ($val->Load_Lines as $Load_Lines) {
                                            $percent = '-';
                                            if ($Load_Lines->Auto != 0 && $Load_Lines->Target != 0) {
                                                $percent = ($Load_Lines->Auto - $Load_Lines->Target) / $Load_Lines->Target * 100;
                                                $minplus = $percent > 0 ? '+' : '';
                                                $percent = $minplus . number_format($percent, 2, ',', '.') . '%';
                                            }

                                            echo $Load_Lines->Item_Code . ' / ' . number_format($Load_Lines->Target, 2, ',', '.') . ' ' . $Load_Lines->Amt_UOM . ' / ' . number_format($Load_Lines->Auto, 2, ',', '.') . ' ' . $Load_Lines->Amt_UOM . ' / ' . $percent . '<br />';
                                        }
                                        ?>
                                    </td>
                                    <td><?= $val->Price_Qty ?> m3</td>
                                    <td><?= (is_null($val->OrderID) || $val->OrderID == '' ? $val->Load_Size : $val->Delivered_Qty) ?> m3</td>
                                    <td><?= $val->CreatedBy ?></td>
                                    <td><?= $val->Driver_Name ?></td>
                                    <td><?= $val->Truck_Code ?></td>
                                    <td><?= date_format(date_create($val->RecordDate), "d/m/Y") . '<br />' .  date_format(date_create($val->RecordDate), "H:i:s") ?></td>
                                    <td><?= $val->bp_name ?></td>
                                    <td><?= $val->Ticket_Status ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>