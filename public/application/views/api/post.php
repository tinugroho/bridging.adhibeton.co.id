<?php

function textDecode($str)
{
    return htmlentities(htmlspecialchars_decode($str, ENT_QUOTES));
}

if ($mesin == 'autobatch') {
    $db_autobatch = $this->load->database('autobatch', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
}

$all_jo = $data_jo;
$key_jo = array_search($JO_Number, array_column($data_jo, 'JO_Number'));
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

$koknversi_not_found = false;
?>


<!-- Page-body start -->
<div class="page-body">
    <!-- Basic table card start -->
    <div class="card">
        <div class="card-header">
            <?= $this->session->flashdata('pesan'); ?>
            <h5>API Post</h5>
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
                            <h5>Record Date</h5>
                        </div>
                        <div class="col-md-8">: <?= $RecordDate ?></div>
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
                </div>
            </div>

        </div>
        <div class="card-block">
            <form method="POST">
                <div class="row mt-1">
                    <div class="col-md-7">
                        <div class="form-group">
                            <label for="JO_Number" class="font-weight-bold">JO Number</label>
                            <input type="text" name="JO_Number" class="form-control" id="JO_Number" value="<?= $JO_Number ?>" readonly>

                            <?php
                            foreach ($data_jo as $key_jo => $val_jo) {
                                echo '<input type="hidden" name="data_jo[' . $key_jo . ']" value="' . textDecode($val_jo) . '" readonly>';
                            }
                            ?>

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
                                <div class="col-md-4">Amount BP</div>
                                <div class="col-md-4">Current Conv</div>
                                <div class="col-md-3">Status</div>
                            </div>
                        </div>
                    </div>
                    <?php
                    // if ($mesin == 'commandbatch') {
                    if (in_array($mesin, ['commandbatch', 'autobatch'])) {
                        if (!empty($materialPerLoad)) {
                            $arrMaterial = $materialPerLoad;
                            $arr_material_akumulasi = [];

                            foreach ($arrMaterial as $index_material => $val) {
                                if (strpos(strtolower($val->Item_Code), 'air') !== false || strpos(strtolower($val->Item_Code), 'water') !== false) {
                                    continue;
                                }

                                if (strtolower($val->Amt_UOM) == 'g') {
                                    $val->Auto = $val->Auto / 1000;
                                    $val->Amt_UOM = 'kg';
                                }

                                $per_material = [];
                                if (mb_substr($val->Item_Code, 0, 1) == '_') {
                                    $per_material[0] = $this->db->query("select Item_Code_ERP from Item_Code_Alias where Item_Code_BP='" . $val->Item_Code . "'")->row()->Item_Code_ERP;
                                } else {
                                    $per_material[0] = $val->Item_Code;
                                }
                                $per_material[0] = str_replace(' ', '', $per_material[0]);
                                $per_material[1] = $val->Auto;
                    ?>
                                <div class="row mt-1">
                                    <div class="col-md-2">
                                        <input type="text" class="form-control" name="" value="<?= $val->Item_Code ?>" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control" name="Material[]" value="<?= $per_material[0] ?>">
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
                                            <div class="col-md-4 px-md-1 d-inline-flex align-items-center">
                                                <input type="text" class="form-control mr-2" name="" value="<?= round($val->Auto, 2) ?>" disabled>
                                                <div><span class="d-block" style="width: 24px;"><?= $val->Amt_UOM ?></span></div>
                                            </div>
                                            <div class="col-md-4 px-md-1 d-inline-flex align-items-center">
                                                <?php

                                                $item_koknversi_not_found = false;
                                                $current_amount = $per_material[1];
                                                $rumus = '';
                                                if (isset($material_satuan[$per_material[0]])) {
                                                    if (strtoupper($material_satuan[$per_material[0]]) == strtoupper($val->Amt_UOM)) {
                                                        $current_amount = $val->Auto;
                                                    } else {
                                                        $key_density = array_search($per_material[0], array_column($data_density_production, 'Item_Code'));
                                                        if ($key_density !== false) {
                                                            $current_amount = $data_density_production[$key_density]['Item_Density'] * $val->Auto / $data_density_production[$key_density]['Scale1'];
                                                            $rumus = $data_density_production[$key_density]['Item_Density'] . '*' . round($val->Auto, 2) . '/' . $data_density_production[$key_density]['Scale1'];
                                                            $current_amount = round($current_amount, 2);
                                                        } else {
                                                            $item_koknversi_not_found = true;
                                                            $koknversi_not_found = true;
                                                        }
                                                    }
                                                } else {
                                                    $item_koknversi_not_found = true;
                                                    $koknversi_not_found = true;
                                                }

                                                if (isset($arr_material_akumulasi[$per_material[0]])) {
                                                    $arr_material_akumulasi[$per_material[0]] = $arr_material_akumulasi[$per_material[0]] + $current_amount;
                                                } else {
                                                    $arr_material_akumulasi[$per_material[0]] = $current_amount;
                                                }

                                                ?>
                                                <input type="text" name="amount[]" class="form-control mr-2" value="<?= round($current_amount, 2) ?>" readonly>
                                                <div><span class="d-block" style="width: 24px;"><?= isset($material_satuan[$per_material[0]]) ? $material_satuan[$per_material[0]] : '' ?></span></div>
                                            </div>
                                            <div class="col-md-3 mb-3 mb-md-0">
                                                <?php
                                                if (!empty($data_jo)) {
                                                    if ($current_amount == 'xxx' || $current_amount == 0) {
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
                            }


                            $material_akumulasi_str = [];
                            foreach ($arr_material_akumulasi as $x => $y) {
                                $material_akumulasi_str[] = $x . '|' . $y;
                            }
                            $material_akumulasi_str = implode('-', $material_akumulasi_str);

                            $typeArr = explode('-', $type);
                            $devOrLive = $typeArr[0] == 'live' ? '' : 'dev';
                            if ($typeArr[1] == 'bspi') {
                                $urlApi = "http://192.168.100.17/apberp" . $devOrLive . "/erp/eaccounting/tools/api/sfservice.cfc?method=setDataBSPI&JO_Number=" . $JO_Number . "&Reference=" . $dataLoad->index_load . "&Trx_Date=" . explode(' ', $RecordDate)[0] . "&Ship_Distance=1&Output_Qty=" . $dataLoad->Load_Size . "&Input_Qty=" . $material_akumulasi_str;
                            } else {
                                $urlApi = "http://192.168.100.17/apberp" . $devOrLive . "/erp/eaccounting/tools/api/sfservice.cfc?method=setDataBSP&JO_Number=" . $JO_Number . "&SO_Number=" . $data_jo['SO_Number'] . "&Reference=" . $dataLoad->index_load . "&Trx_Date=" . explode(' ', $RecordDate)[0] . "&Ship_Distance=1&Output_Qty=" . $dataLoad->Load_Size . "&Input_Qty=" . $material_akumulasi_str . "&Driver=" . $dataLoad->Driver_Code . "&Vehicle_No=" . $dataLoad->Truck_Code . "&Trx_ID=" . $dataLoad->Ticket_Code;
                            }
                            echo '<div class="mt-3"><small>' . $urlApi . '</small></div>';
                        }
                    }
                    ?>
                </div>
                <?php

                if ($mesin == 'commandbatch') {
                    $pernah_post = $this->db->query("SELECT API_Logs_Detail.index_load FROM API_Logs_Detail WHERE API_Logs_Detail.index_load=$Index_Load;")->result();
                } else if ($mesin == 'autobatch') {
                    $pernah_post = $db_autobatch->query("SELECT API_Logs_Detail.index_load FROM API_Logs_Detail WHERE API_Logs_Detail.index_load=$Index_Load;")->result();
                } else {
                    $pernah_post = '';
                }

                if (!empty($pernah_post)) {
                    echo '<div class="alert alert-success">Data JO sudah dipost.</div>';
                } else {
                    $pernah_gagal = '';
                    if ($mesin == 'commandbatch') {
                        $pernah_gagal = $this->db->query("SELECT API_Post_Gagal.Index_Post_Gagal FROM API_Post_Gagal WHERE API_Post_Gagal.index_load=$Index_Load;")->result();
                    } else if ($mesin == 'autobatch') {
                        $pernah_gagal = $db_autobatch->query("SELECT API_Post_Gagal.Index_Post_Gagal FROM API_Post_Gagal WHERE API_Post_Gagal.index_load=$Index_Load;")->result();
                    }

                    if (!empty($pernah_gagal)) {
                        // print_r($pernah_gagal);
                        echo '<div class="alert alert-danger">Data JO gagal dipost.</div>';
                        echo '<a class="btn btn-warning" href="' . base_url('Api/repost') . '?type=' . $type . '&Index_Post_Gagal=' . $pernah_gagal[0]->Index_Post_Gagal . '&mesin=' . $mesin . '">Detail Repost</a>';
                    } else {
                        if (empty($data_jo)) {
                            echo '<div class="alert alert-danger">Terjadi kesalahan, data JO tidak ditemukan di API ERP.</div>';
                        } else if ($invalid_material_code) {
                            // echo '<div class="alert alert-danger">Terjadi kesalahan, material code tidak sesuai dengan API ERP.</div>';
                            echo '<div class="alert alert-danger">Terjadi kesalahan, material code tidak sesuai dengan API ERP. Silahkan Edit Material Code sesuai API ERP.</div>';
                            echo '<button type="submit" name="update" class="btn btn-primary">Update</button>';
                        } else if ($invalid_material_amount) {
                            echo '<div class="alert alert-danger">Terjadi kesalahan, material amount 0 atau GetDataDensity tidak ditemukan.</div>';
                        } else if (!in_array($mesin, ['commandbatch', 'autobatch'])) {
                            echo '<div class="alert alert-danger">Eurotech has not ready.</div>';
                        } else {
                            echo '<button type="submit" name="post" class="btn btn-primary">Post</button>';
                        }
                    }
                }


                ?>
            </form>
        </div>
    </div>
</div>

<?php

// echo '<pre>';
// print_r($data_density);
// print_r($all_jo);
// print_r($_POST);
// print_r($this->db->queries);
// echo '</pre>';

// echo '<pre>';
// print_r($dataLoad);
// echo '<pre>';
?>