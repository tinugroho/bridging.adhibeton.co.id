<?php
if ($mesin == 'commandbatch') {
    $dataLoad = $this->db->query("select a.Ticket_Code, b.bp_name from V_BatchSetupTickets a inner join Batching_plant b ON a.BP_ID=b.id_bp where a.index_load=" . $Index_Load)->result()[0];
} else if ($mesin == 'autobatch') {
    $db_autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
    $dataLoad = $db_autobatch->query("select a.Ticket_Id 'Ticket_Code', b.bp_name from TICKET a inner join Batching_plant b ON a.BP_ID=b.id_bp where a.index_load=" . $Index_Load)->result()[0];
}

$key_jo = array_search($JO_Number, array_column($data_jo, 'JO_Number'));
$all_jo = $data_jo;
if ($key_jo !== false) {
    $data_jo = $data_jo[$key_jo];
} else {
    $alert .= '<li>Data JO tidak ditemukan.</li>';
    $warna = 'danger';
    $data_jo = [];
}

if (!empty($Material)) {
    if (strpos($Material, 'xxx') !== false) {
        $alert .= '<li>Konversi gagal pada proses sebelumnya, GetDataDensity tidak ditemukan.</li>';
        $warna = 'danger';
    }
}
$invalid_material_code = false;
$invalid_material_amount = false;

$data_density_production = [];
foreach ($data_density as $density) {
    if ($density['Doc_Type'] == 'Production') {
        $data_density_production[] = $density;
    }
}
?>


<!-- Page-body start -->
<div class="page-body">
    <!-- Basic table card start -->
    <div class="card">
        <div class="card-header">
            <?= $this->session->flashdata('pesan'); ?>
            <h5>API Repost</h5>
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
            <?php
            echo $alert == '' ? '' : '<div class="alert alert-' . $warna . ' alert-dismissible fade show" role="alert">
                                        <ul>' . $alert . '</ul>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>';
            ?>
            <div class="row">
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Post Date</h5>
                        </div>
                        <div class="col-md-8">: <?= $Running_Date ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Record Date</h5>
                        </div>
                        <div class="col-md-8">: <?= $RecordDate ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Keterangan</h5>
                        </div>
                        <div class="col-md-8">:
                            <?php
                            switch (strtolower($Keterangan)) {
                                case '':
                                    break;
                                case 'posted':
                                    echo '<div class="badge badge-success">Posted</div>';
                                    break;

                                default:
                                    echo '<div class="badge badge-danger">' . $Keterangan . '</div>';
                                    break;
                            }
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Index Load</h5>
                        </div>
                        <div class="col-md-7">: <?= $Index_Load ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5 class="font-weight-bold">Load Size</h5>
                        </div>
                        <div class="col-md-8">: <?= !empty($Load_Size) ? $Load_Size . ' m3' : '' ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5 class="font-weight-bold">Type</h5>
                        </div>
                        <div class="col-md-8">: <?= $type ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5 class="font-weight-bold">Mesin</h5>
                        </div>
                        <div class="col-md-8">: <?= $mesin ?></div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-md-4">
                            <h5 class="font-weight-bold">No Tiket</h5>
                        </div>
                        <div class="col-md-8">: <?= $dataLoad->Ticket_Code ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5 class="font-weight-bold">No Referensi</h5>
                        </div>
                        <div class="col-md-8">: <?= $Index_Load ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5 class="font-weight-bold">Kode BP</h5>
                        </div>
                        <div class="col-md-8">: <?= $dataLoad->bp_name ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5 class="font-weight-bold">Jumlah Post</h5>
                        </div>
                        <div class="col-md-8">: <?= $detailPostGagal->jml_post ?></div>
                    </div>
                </div>
            </div>

        </div>
        <div class="card-block">
            <form method="POST">
                <div class="row mt-1">
                    <div class="col-md-7">
                        <div class="form-group">
                            <label for="JO_Number" class="font-weight-bold">JO Number</label>
                            <input type="text" name="JO_Number" class="form-control" id="JO_Number" value="<?= $JO_Number ?>" <?= $Keterangan != 'POSTED' ? '' : 'readonly' ?>>

                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Material Dibutuhkan : </label>
                    <?php
                    $material_satuan = [];
                    $no_material = 0;
                    $str_material_dibutuhkan = '';
                    if (!empty($data_jo)) {
                        $arr_material_code = explode('|', $data_jo['Material_Code']);
                        $arr_material_unit = explode('|', $data_jo['Material_Unit']);
                        foreach ($arr_material_code as $material_code) {
                            $material_satuan[$material_code] = $arr_material_unit[$no_material];
                            $str_material_dibutuhkan .= ', ' . $material_code . ' (' . $arr_material_unit[$no_material] . ')';
                            $no_material++;
                        }
                    }
                    echo ltrim($str_material_dibutuhkan, ",");
                    ?>
                    <br>
                    <!-- <label class="font-weight-bold">Material</label> -->
                    <div class="row" style="font-size: 12;">
                        <div class="col-md-2">Material BP</div>
                        <div class="col-md-2">Material Post ERP</div>
                        <div class="col-md-2">Status</div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">Amount BP</div>
                                <div class="col-md-3">Last Post Conv</div>
                                <div class="col-md-3">Current Conv</div>
                                <div class="col-md-3">Status</div>
                            </div>
                        </div>
                    </div>
                    <?php
                    // if ($mesin == 'commandbatch') {
                    $arr_material_akumulasi = [];
                    if (in_array($mesin, ['commandbatch', 'autobatch'])) {
                        if (!empty($Material)) {
                            $arrMaterial = explode('-', $Material);
                            $per_materials = [];
                            foreach ($arrMaterial as $pasanganMaterialKeyVal) {
                                $materialKeyVal = explode('|', $pasanganMaterialKeyVal);
                                $per_materials[] = $materialKeyVal;
                            }

                            $no = 0;

                            foreach ($materialPerLoad as $index_materialPerLoad => $materialBP) {
                                if (strpos(strtolower($materialBP->Item_Code), 'air') !== false || strpos(strtolower($materialBP->Item_Code), 'water') !== false) {
                                    unset($materialPerLoad[$index_materialPerLoad]);
                                }
                                if (strtolower($materialBP->Amt_UOM) == 'g') {
                                    $materialPerLoad[$index_materialPerLoad]->Auto = $materialBP->Auto / 1000;
                                    $materialPerLoad[$index_materialPerLoad]->Amt_UOM = 'kg';
                                }
                            }
                            $materialPerLoad = array_values($materialPerLoad);

                            // foreach ($arrMaterial as $index_material => $val) {
                            foreach ($materialPerLoad as $index_material => $val) {
                                if (isset($per_materials[$index_material])) {
                                    $per_material = $per_materials[$index_material];
                                    $per_material[0] = str_replace(' ', '', $per_material[0]);
                                    // } else if ($val->Item_Code[0] == '_' && isset($per_materials[$val->Alias])) {
                                    //     $per_material = $per_materials[$val->Alias];
                                } else {
                                    $per_material = ['', 0];
                                }
                    ?>
                                <div class="row mt-1">
                                    <div class="col-md-2">
                                        <input type="text" class="form-control" name="" value="<?= $materialPerLoad[$no]->Item_Code ?>" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control" name="Material[]" value="<?= $per_material[0] ?>" <?= $Keterangan != 'POSTED' ? '' : 'readonly' ?>>
                                    </div>
                                    <div class="col-md-2 mb-3 mb-md-0">
                                        <?php
                                        if (!empty($data_jo)) {
                                            $material_dibutuhkan = explode('|', $data_jo['Material_Code']);
                                            if (in_array($per_material[0], $material_dibutuhkan)) {
                                                echo '<span class="badge badge-success">Pass</span>';
                                            } else {
                                                echo '<span class="badge badge-danger">Invalid</span>';
                                                $invalid_material_code = true;
                                            }
                                        }
                                        ?>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-3 px-md-1 d-inline-flex align-items-center">
                                                <input type="text" class="form-control mr-2" name="" value="<?= $materialPerLoad[$no]->Auto ?>" disabled>
                                                <div><span class="d-block" style="width: 24px;"><?= $materialPerLoad[$no]->Amt_UOM ?></span></div>
                                            </div>
                                            <div class="col-md-3 px-md-1 d-inline-flex align-items-center">
                                                <input class="mr-1" type="radio" name="amount[<?= $index_material ?>]" value="<?= round($per_material[1], 2) ?>">
                                                <input type="text" class="form-control mr-2" value="<?= round($per_material[1], 2) ?>" readonly>
                                                <div><span class="d-block" style="width: 24px;"><?= isset($material_satuan[$per_material[0]]) ? $material_satuan[$per_material[0]] : '' ?></span></div>
                                            </div>
                                            <div class="col-md-3 px-md-1 d-inline-flex align-items-center">
                                                <?php
                                                $item_koknversi_not_found = false;
                                                $current_amount = $per_material[1];
                                                $rumus = '';
                                                if ($materialPerLoad[$no]->Auto > 0) {
                                                    if (isset($material_satuan[$per_material[0]])) {
                                                        if (strtoupper($material_satuan[$per_material[0]]) == strtoupper($materialPerLoad[$no]->Amt_UOM)) {
                                                            $current_amount = $materialPerLoad[$no]->Auto;
                                                        } else {
                                                            $key_density = array_search($per_material[0], array_column($data_density_production, 'Item_Code'));
                                                            if ($key_density !== false) {
                                                                $current_amount = $data_density_production[$key_density]['Item_Density'] * $materialPerLoad[$no]->Auto / $data_density_production[$key_density]['Scale1'];
                                                                $rumus = $data_density_production[$key_density]['Item_Density'] . '*' . $materialPerLoad[$no]->Auto . '/' . $data_density_production[$key_density]['Scale1'];
                                                                $current_amount = round($current_amount, 2);
                                                            } else {
                                                                $item_koknversi_not_found = true;
                                                            }
                                                        }
                                                    } else {
                                                        $item_koknversi_not_found = true;
                                                    }
                                                }

                                                if (isset($arr_material_akumulasi[$per_material[0]])) {
                                                    $arr_material_akumulasi[$per_material[0]] = $arr_material_akumulasi[$per_material[0]] + $current_amount;
                                                } else {
                                                    $arr_material_akumulasi[$per_material[0]] = $current_amount;
                                                }

                                                ?>
                                                <input class="mr-1" type="radio" name="amount[<?= $index_material ?>]" value="<?= $current_amount ?>" checked>
                                                <input type="text" class="form-control mr-2" value="<?= $current_amount ?>" readonly>
                                                <div><span class="d-block" style="width: 24px;"><?= isset($material_satuan[$per_material[0]]) ? $material_satuan[$per_material[0]] : '' ?></span></div>
                                            </div>
                                            <div class="col-md-3 mb-3 mb-md-0">
                                                <?php
                                                if (!empty($data_jo)) {
                                                    if ($materialPerLoad[$no]->Auto == 0) {
                                                        echo '<span class="badge badge-success">Pass</span>';
                                                    } else if ($current_amount == 'xxx' || $current_amount == 0) {
                                                        echo '<span class="badge badge-danger">Invalid</span>';
                                                        $invalid_material_amount = true;
                                                    } else if ($item_koknversi_not_found) {
                                                        echo '<span class="badge badge-danger">Conversion Not Found</span>';
                                                        $invalid_material_amount = true;
                                                    } else {
                                                        echo '<span class="badge badge-success">Pass</span><small> ' . $rumus . '</small>';
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="d-md-none mb-3 mt-0">
                    <?php
                                $no++;
                            }
                        }
                    }
                    //  else {
                    // echo '<pre>';
                    // print_r($materialPerLoad);
                    // echo '</pre>';
                    // }
                    ?>
                </div>
                <?php
                if (empty($data_jo)) {
                    echo '<div class="alert alert-danger">Terjadi kesalahan, data JO tidak ditemukan di API ERP.</div>';
                    if (strtoupper($Keterangan) != 'POSTED') {
                        echo '<button type="submit" name="update" class="btn btn-primary">Update</button>';
                    }
                } else if ($invalid_material_code) {
                    echo '<div class="alert alert-danger">Terjadi kesalahan, material code tidak sesuai dengan API ERP. Silahkan Edit Material Code sesuai API ERP.</div>';
                    echo '<button type="submit" name="update" class="btn btn-primary">Update</button>';
                } else if ($invalid_material_amount) {
                    echo '<div class="alert alert-danger">Terjadi kesalahan, material amount 0 atau GetDataDensity tidak ditemukan.</div>';
                } else if (!in_array($mesin, ['commandbatch', 'autobatch'])) {
                    echo '<div class="alert alert-danger">Eurotech has not ready.</div>';
                } else if (strtoupper($Keterangan) != 'POSTED') {
                    $typeArr = explode('-', $type);
                    $devOrLive = $typeArr[0] == 'live' ? '' : 'dev';

                    $material_akumulasi_str = [];
                    foreach ($arr_material_akumulasi as $x => $y) {
                        $material_akumulasi_str[] = $x . '|' . $y;
                    }
                    $material_akumulasi_str = implode('-', $material_akumulasi_str);

                    if ($typeArr[1] == 'bspi') {
                        $urlApi = "http://192.168.100.17/apberp" . $devOrLive . "/erp/eaccounting/tools/api/sfservice.cfc?method=setDataBSPI&JO_Number=" . $JO_Number . "&Reference=" . $Index_Load . "&Trx_Date=" . explode(' ', $RecordDate)[0] . "&Ship_Distance=1&Output_Qty=" . $Load_Size . "&Input_Qty=" . $material_akumulasi_str;
                    } else {
                        $urlApi = "http://192.168.100.17/apberp" . $devOrLive . "/erp/eaccounting/tools/api/sfservice.cfc?method=setDataBSP&JO_Number=" . $JO_Number . "&SO_Number=" . $data_jo['SO_Number'] . "&Reference=" . $Index_Load . "&Trx_Date=" . explode(' ', $RecordDate)[0] . "&Ship_Distance=1&Output_Qty=" . $Load_Size . "&Input_Qty=" . $material_akumulasi_str . "&Driver=" . $detailPostGagal->Driver_Code . "&Vehicle_No=" . $detailPostGagal->Truck_Code . "&Trx_ID=" . $detailPostGagal->Ticket_Code;
                    }
                    echo '<div class="my-3"><small>' . $urlApi . '</small></div>';
                ?>
                    <button type="submit" name="update" class="btn btn-primary mr-2">Update</button>
                    <button type="submit" name="repost" class="btn btn-success">Post Ulang</button>
                <?php
                }

                if (strtoupper($Keterangan) != 'POSTED') {
                    echo '<button type="submit" name="delete" class="btn btn-danger float-right" onclick="return confirm(\'Hapus dan lakukan perhitungan ulang di menu post manual?\')">Delete, Post Manual</button>';
                }
                ?>
            </form>
        </div>
    </div>
</div>

<?php
// echo '<pre>';
// print_r($data_density_production);
// print_r($Material);
// print_r($this->db->queries);
// echo '</pre>';

?>