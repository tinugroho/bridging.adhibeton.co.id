<!-- <div class="container-fluid"> -->

<div class="page-body">
    <!-- Basic table card start -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <?= $this->session->flashdata('pesan'); ?>
                <!-- <a href="" class="btn btn-primary" data-toggle="modal" data-target="#addRole"><i class="fas fa-plus mr-3"> Add</i></a> -->
                <h4><?= $judul; ?></h4>

                <div class="row mt-5">
                    <div class="col-lg-10">
                        <form class="form-inline">
                            <div class="form-group">
                                <label for="from">From</label>
                                <input type="datetime-local" id="from" name="from" class="form-control mx-sm-3">
                                <label for="to">To</label>
                                <input type="datetime-local" id="to" name="to" class="form-control mx-sm-3">
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

            <div class="card-block table-border-style" style="font-size: small;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>no ticket</th>
                                <th>no JO</th>
                                <th>Nama JMF</th>
                                <!-- <th>req_vol</th> -->
                                <th>Nama customer</th>
                                <th>Ticket date</th>
                                <th>Total load</th>
                                <th>Total bath</th>
                                <th>Akumulatif volume</th>
                                <th>status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            ?>
                            <tr>
                                <th><?= $no++; ?></th>
                                <td>
                                    dasdjf<br> asdfjkaslfhasklfhaklfasd
                                </td>
                                <td>dasdjasdvdsvdsvsdv</td>
                                <td>dasdjasdvdvsdvsv</td>
                                <td>dasdjasdvdsvdsvdsvs</td>
                                <td>dasdjasdvdvdsvds</td>
                                <!-- <td>dasdjasd</td> -->

                                <td><a href="<?= base_url('aceh/aceh/detail_load'); ?>" target="blank" class="btn btn-info badge">detail</a></td>
                                <td><a href="<?= base_url('aceh/aceh/detail_batch'); ?>" target="blank" class="btn btn-info badge">detail</a></td>

                                <td>dasdjasd</td>
                                <td>dasdjasd</td>

                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- </div> -->