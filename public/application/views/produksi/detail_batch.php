<div class="page-body">
    <!-- Basic table card start -->
    <div class="col-md-12">
        <div class="card py-3">
            <div class="card-header px-3 py-0">
                <h4><small>Jobmix Updates</small></h4>
            </div>
            <div class="card-block table-border-style">
                <div class="table-responsive">
                    <table class="table table-only table-striped table-bordered" style="font-size: small;">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Volume</th>
                                <th>
                                    <div class="row m-0">
                                        <div class="col-3 p-0">Material Code</div>
                                        <div class="col-7 p-0">Material Desc</div>
                                        <div class="col-2 p-0">Qty</div>
                                    </div>
                                </th>
                                <th>UpdatedBy</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($jobmix as $val) {
                                echo "<tr>";
                                echo "<td>" . $val->RecordDate . "</td>";
                                echo "<td>" . $val->volume . "</td>";

                                echo '<td>';
                                $arrBahan = explode('<br>', $val->bahan);
                                foreach ($arrBahan as $bahan_val) {
                                    $bahan = explode('|', $bahan_val);
                                    echo '<div class="row m-0">';
                                    echo '<div class="col-3 p-0 border border-top-0 border-left-0 border-right-0">' . $bahan[0] . '</div>';
                                    echo '<div class="col-7 p-0 border border-top-0 border-left-0 border-right-0">' . $bahan[1] . '</div>';
                                    echo '<div class="col-2 p-0 border border-top-0 border-left-0 border-right-0">' . $bahan[2] . ' ' . $bahan[3] . '</div>';
                                    echo '</div>';
                                }
                                echo '</td>';

                                echo "<td>" . $val->UpdatedBy . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header px-3 pt-3 pb-0">
                <h4><small>Detail Batch</small></h4><br>
                <table class="mb-4">
                    <tr>
                        <td class="pr-4">No JO</td>
                        <td>: <?= empty($data) ? '' : reset($data)->Delivery_Instruction ?></td>
                    </tr>
                    <tr>
                        <td class="pr-4">JMF</td>
                        <td>: <?= empty($data) ? '' : reset($data)->Jobmix ?></td>
                    </tr>
                    <tr>
                        <td class="pr-4">Customer</td>
                        <td>: <?= empty($data) ? '' : reset($data)->Customer_Description ?></td>
                    </tr>
                </table>
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
                    <table class="table table-striped table-bordered zero-configuration" style="font-size: small;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No<br>Tiket</th>
                                <th>No<br>Batch</th>
                                <th>Material Usage<br>Material/Target/Actual/Percent</th>
                                <th>Vol</th>
                                <th>Operator<br>BP</th>
                                <th>Record<br>Date</th>
                                <th>BP<br>Name</th>
                                <!-- <th>Status</th> -->

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($data as $batch) {

                                echo "<tr>";
                                echo "<td>" .  $no++ . "</td>";
                                echo "<td>" . $batch->Ticket_Code . "</td>";
                                echo "<td>" . $batch->Batch_Num . "</td>";
                                echo "<td>";

                                $Batch_Lines = explode('-', $batch->item);
                                sort($Batch_Lines);
                                foreach ($Batch_Lines as $Batch_Line) {
                                    $item = explode('|', $Batch_Line);
                                    $percent = '-';
                                    $item[0] = isset($item[0]) ? $item[0] : '';
                                    $item[1] = isset($item[1]) ? $item[1] : 0;
                                    $item[2] = isset($item[2]) ? $item[2] : 0;
                                    $item[3] = isset($item[3]) ? $item[3] : '';
                                    if ($item[2] != 0 && $item[1] != 0) {
                                        $percent = ($item[2] - $item[1]) / $item[1] * 100;
                                        $minplus = $percent > 0 ? '+' : '';
                                        $percent = $minplus . number_format($percent, 2, ',', '.') . '%';
                                    }

                                    // echo $item[0] . ' / ' . number_format($item[1], 2, ',', '.') . ' ' . $item[3] . ' / ' . number_format($item[2], 2, ',', '.') . ' ' . $item[3] . ' / ' . $percent . '<br />';
                                    echo '<div class="row m-0"><div class="col-4 p-0">' . $item[0] . ' </div><div class="col-3 p-0 text-right"> ' . number_format($item[1], 2, ',', '.') . ' ' . $item[3] . ' </div><div class="col-3 p-0 text-right"> ' . number_format($item[2], 2, ',', '.') . ' ' . $item[3] . '</div> <div class="col-2 p-0 text-right"> ' . $percent . '</div></div>';
                                }

                                echo "</td>";
                                echo "<td>" . $batch->Batch_Size . " m3</td>";
                                echo "<td>" . $batch->CreatedBy . "</td>";
                                echo "<td>" . date_format(date_create($batch->RecordDate), "Y/m/d") . '<br />' .  date_format(date_create($batch->RecordDate), "H:i:s") . "</td>";
                                echo "<td>" . $batch->bp_name . "</td>";
                                // if ($batch->Delivery_Instruction == '') {
                                //     echo '<td style="text-align:center">Not Posted</td>';
                                // } else if ($batch->api_sukses != '') {
                                //     echo '<td style="text-align:center">Posted</td>';
                                // } else if ($batch->api_gagal != '') {
                                //     echo '<td style="text-align:center"><a class="btn btn-primary btn-sm" href="/Api/repost?type=' . $batch->api_gagal_type . '&Index_Post_Gagal=' . $batch->api_gagal . '&mesin=' . $this->uri->segment(4) . '">Repost</a></td>';
                                // } else {
                                //     echo '<td style="text-align:center">Api<br>Waiting</td>';
                                // }
                                echo "</tr>";
                            }
                            ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>