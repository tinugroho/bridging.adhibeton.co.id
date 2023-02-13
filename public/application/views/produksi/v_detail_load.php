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
                                    echo '<div class="col-2 p-0 border border-top-0 border-left-0 border-right-0">' . $bahan[2] . ' ' . @$bahan[3] . '</div>';
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
        <!-- <pre> -->
        <?php
        // print_r($data);
        ?>
        <!-- </pre> -->
        <div class="card">
            <div class="card-header px-3 pt-3 pb-0">
                <h4><small>Detail Load</small></h4><br>
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-3 pr-4">No JO</div>
                            <div class="col-sm-9">: <?= empty($data) ? '' : reset($data)->Delivery_Instruction ?></div>

                            <div class="col-sm-3 pr-4">JMF</div>
                            <div class="col-sm-9">: <?= empty($data) ? '' : reset($data)->Item_Code ?></div>

                            <div class="col-sm-3 pr-4">Customer</div>
                            <div class="col-sm-9">: <?= empty($data) ? '' : reset($data)->Customer_Description ?></div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-3 pr-4">
                                <?php
                                if ($this->uri->segment(4) == 'commandbatch') {
                                    echo 'No Ticket';
                                } else
                                    if ($this->uri->segment(4) == 'autobatch') {
                                    echo 'Ticket Id';
                                } else
                                    if ($this->uri->segment(4) == 'eurotech') {
                                    echo 'No Sheet';
                                }
                                ?>
                            </div>
                            <div class="col-sm-9"> :
                                <?php
                                if (!empty($data)) {
                                    if ($this->uri->segment(4) == 'commandbatch') {
                                        echo reset($data)->Ticket_Code;
                                    } else
                                    if ($this->uri->segment(4) == 'autobatch') {
                                        echo reset($data)->max_ticket;
                                    } else
                                    if ($this->uri->segment(4) == 'eurotech') {
                                        echo reset($data)->sheet_no;
                                    }
                                }
                                ?>
                            </div>

                            <div class="col-sm-3 pr-4">No SKLP</div>
                            <div class="col-sm-9">:
                                <?php
                                if (!empty($data)) {
                                    if ($this->uri->segment(4) == 'commandbatch') {
                                        if (reset($data)->PO_Num == '') {
                                            echo reset($data)->PO_Num;
                                        } else {
                                            echo reset($data)->Job_Code;
                                        }
                                    }
                                    if ($this->uri->segment(4) == 'autobatch') {
                                        echo reset($data)->sklp;
                                    }
                                }
                                ?>
                            </div>

                            <div class="col-sm-3 pr-4">Kode BP</div>
                            <div class="col-sm-9">: <?= empty($data) ? '' : reset($data)->bp_name ?></div>
                        </div>
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
            <!-- <h2 class="text-center" style="font-size: small;">load</h2> -->
            <div class="card-block table-border-style" style="font-size: x-small;">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered zero-configuration" style="font-size: small;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No<br>Tiket</th>
                                <th>No<br>Load</th>
                                <th>Material Usage<br>Material/Target/Actual/Percent</th>
                                <th>Vol</th>
                                <!-- <th>req_vol</th> -->
                                <th>Akumulasi</th>
                                <th>Operator</th>
                                <th>Driver<br>Truck</th>
                                <th>Date</th>
                                <!-- <th>BP<br>Name</th> -->
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
                                    <td>
                                        <b>No Tikcet:</b><br>
                                        <p><?= $val->Ticket_Code ?></p>
                                        <b>No Referensi:</b><br>
                                        <?= $mesin == 'eurotech' ? $val->index : $val->index_load ?>
                                    </td>
                                    <td><?= $val->Order_Number_Of_Tickets ?></td>
                                    <td>
                                        <?php
                                        $Load_Lines = explode('~', $val->item);
                                        foreach ($Load_Lines as $Load_Line) {
                                            $item = explode('|', $Load_Line);
                                            $percent = '-';
                                            if ($item[2] != 0 && $item[1] != 0) {
                                                $percent = ($item[2] - $item[1]) / $item[1] * 100;
                                                $minplus = $percent > 0 ? '+' : '';
                                                $percent = $minplus . number_format($percent, 2, ',', '.') . '%';
                                            }

                                            // echo $item[0] . ' / ' . number_format($item[1], 2, ',', '.') . ' ' . $item[3] . ' / ' . number_format($item[2], 2, ',', '.') . ' ' . $item[3] . ' / ' . $percent . '<br />';
                                            echo '<div class="row m-0"><div class="col-4 p-0">' . $item[0] . ' </div><div class="col-3 p-0 text-right"> ' . number_format($item[1], 2, ',', '.') . ' ' . $item[3] . ' </div><div class="col-3 p-0 text-right"> ' . number_format($item[2], 2, ',', '.') . ' ' . $item[3] . '</div> <div class="col-2 p-0 text-right"> ' . $percent . '</div></div>';
                                        }
                                        ?>
                                    </td>
                                    <td><?= $val->Price_Qty ?> m3</td>
                                    <td><?= (is_null($val->OrderID) || $val->OrderID == '' ? $val->Load_Size : $val->Delivered_Qty) ?> m3</td>
                                    <td><?= $val->CreatedBy ?></td>
                                    <td>
                                        <b>Driver:</b><br>
                                        <p><?= $val->Driver_Name ?></p>
                                        <b>Truck:</b><br>
                                        <?= $val->Truck_Code ?>
                                    </td>
                                    <td><?= date_format(date_create($val->RecordDate), "Y/m/d") . '<br />' .  date_format(date_create($val->RecordDate), "H:i:s") ?></td>
                                    <!-- <td><?= $val->bp_name ?></td> -->
                                    <td style="text-align:center">
                                        <?php
                                        if ($mesin == 'commandbatch') {
                                            $this_db = $this->db;
                                        } else if ($mesin == 'autobatch') {
                                            $this_db = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
                                        }

                                        if ($mesin == 'eurotech') {
                                            echo 'Eurotech not<br>ready for<br>this feature.';
                                        } else if (empty($val->Delivery_Instruction)) {
                                            // JO Kosong
                                            // echo "Not Posted";
                                            echo '  <button type="button" class="btn btn-primary btn-sm mt-1" data-toggle="modal" data-target="#exampleModal">
                                                        Edit JO
                                                    </button>';
                                        } else if (!empty($val->api_sukses)) {
                                            // JO Sudah Dipost
                                            echo "Posted";
                                        } else if (!empty($val->api_gagal)) {
                                            // JO Gagal Dipost
                                            echo '<a class="btn btn-primary btn-sm" href="/Api/repost?type=' . $val->api_gagal_type . '&Index_Post_Gagal=' . $val->api_gagal . '&mesin=' . $this->uri->segment(4) . '">Repost</a>';
                                        } else if (!empty($val->api_sukses_last_id)) {
                                            // JO Valid, pernah dipost load lain
                                            if ($val->index_load < $val->api_sukses_last_id) {
                                                // JO ke skip crontab
                                                $jo_exist = $this_db->query("select JO_Number from API_Logs_Header where JO_Number='$val->Delivery_Instruction'")->result();
                                                if (empty($jo_exist)) {
                                                    // JO Sudah Tak Aktif di API
                                                    echo 'JO Number<br>Sudah Tidak<br>Tersedia di<br>API Active.<br>';
                                                    echo '  <button type="button" class="btn btn-primary btn-sm mt-1" data-toggle="modal" data-target="#exampleModal"> 
                                                        Edit JO
                                                        </button>';
                                                } else {
                                                    // Post Manual
                                                    // echo '<a class="btn btn-primary btn-sm" href="#">Post</a>';
                                                    echo '<a class="btn btn-primary btn-sm" target="_blank" href="/Api/post?type=' . $val->api_type . '&Index_Load=' . $val->index_load . '&mesin=' . $this->uri->segment(4) . '">Post</a>';
                                                }
                                            } else {
                                                // JO Belum dieksekusi crontab
                                                $jo_exist = $this_db->query("select JO_Number from API_Logs_Header where JO_Number='$val->Delivery_Instruction'")->result();
                                                if (empty($jo_exist)) {
                                                    // JO Sudah Tak Aktif di API
                                                    echo 'JO Number<br>Sudah Tidak<br>Tersedia di<br>API Active.<br>';
                                                    echo '  <button type="button" class="btn btn-primary btn-sm mt-1" data-toggle="modal" data-target="#exampleModal">
                                                        Edit JO
                                                        </button>';
                                                } else {
                                                    // JO Nunggu Giliran
                                                    echo 'Api<br>Waiting';
                                                }
                                            }
                                        } else {
                                            // JO Perlu dicek di API, belum pernah dipost load lain
                                            $jo_exist = $this_db->query("select JO_Number from API_Logs_Header where JO_Number='$val->Delivery_Instruction'")->result();
                                            // print_r($jo_exist);
                                            if (empty($jo_exist)) {
                                                // JO Tidak Ditemukan di API
                                                echo 'JO Number<br>Not Found<br>in<br>API Active.<br>';
                                                echo '  <button type="button" class="btn btn-primary btn-sm mt-1" data-toggle="modal" data-target="#exampleModal">
                                                        Edit JO
                                                        </button>';
                                            } else {
                                                // JO Valid, Nunggu Giliran
                                                echo 'Api<br>Waiting';
                                            }
                                        }
                                        ?>
                                    </td>
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

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="post" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update JO Number</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="input_jo_number">JO Number</label>
                    <input type="text" name="JO_Number" class="form-control" id="input_jo_number" value="<?= empty($data) ? '' : reset($data)->Delivery_Instruction ?>" placeholder="Enter JO Number">
                </div>
                <?php
                if (!empty(reset($data)->Delivery_Instruction)) {
                ?>
                    <!-- <div class="form-check">
                        <label class="form-check-label" for="exampleCheck1">
                            <input name="change_all" value="<?= reset($data)->Delivery_Instruction ?>" type="checkbox" class="form-check-input" id="exampleCheck1">
                            Edit semua produksi JO Number <b><?= reset($data)->Delivery_Instruction ?></b><br><small>* pastikan belum ada JO Number <b><?= reset($data)->Delivery_Instruction ?></b> yang dipost</small>
                        </label>
                    </div> -->
                <?php
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</div>