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
                <h4><?= $judul . ' (' . ucfirst($mesin) . ')' ?></h4><br>

                <?= $tabs ?>

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
                    <table id="tabel_jobmix" class="table table-striped table-bordered" style="font-size: small;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Regional</th>
                                <th>BP</th>
                                <th>Jobmix</th>
                                <th>Update</th>
                                <th>Last Update</th>
                                <th>Updated By</th>
                                <th>Option</th>
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