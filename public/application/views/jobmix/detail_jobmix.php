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
                <h4> Jobmix <?= $jobmix_code . ' - ' . $region_bp; ?></h4>

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
                    <table class="table zero-configuration table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
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
                            $no = 1;
                            foreach ($data as $val) {
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
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
    </div>
</div>
<!-- </div> -->