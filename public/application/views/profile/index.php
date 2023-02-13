<div class="col-md-12 col-xl-6">
    <div class="card project-task">
        <div class="card-header">
            <div class="card-header-left ">

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
        <div class="card-block p-b-10">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Your Profiles</th>
                            <th>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="page-header-breadcrumb">
                                            <ul class="breadcrumb-title">
                                                <li class="breadcrumb-item">
                                                    <!-- <a href="">
                                                        <i class="icofont icofont-home ml-auto">home</i>
                                                    </a> -->
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="task-contain">
                                    <?php if ($user['id_role'] == '1') : ?>
                                        <h6 class="bg-c-blue d-inline-block text-center">A</h6>
                                    <?php else : ?>
                                        <h6 class="bg-c-yellow d-inline-block text-center">M</h6>
                                    <?php endif; ?>
                                    <p class="d-inline-block m-l-20"><?= $user['role']; ?></p><br>
                                </div>
                            </td>
                            <td>
                                <!-- <p class="d-inline-block m-r-20">4 : 36</p>
                                <div class="progress d-inline-block">
                                    <div class="progress-bar bg-c-blue" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:80%">
                                    </div>
                                </div> -->
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="task-contain">
                                    <img src="<?= base_url('assets/img/username.jpg'); ?>" data-toggle="tooltip" title="<?= $user['username']; ?>" class="img-40" alt="">
                                    <p class="d-inline-block m-l-20"><?= $user['username']; ?></p>

                                </div>
                            </td>
                            <td>
                                <!-- <p class="d-inline-block m-r-20">4 : 36</p>
                                <div class="progress d-inline-block">
                                    <div class="progress-bar bg-c-pink" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:60%">
                                    </div>
                                </div> -->
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="task-contain">
                                    <img src="<?= base_url('assets/img/email.png'); ?>" data-toggle="tooltip" title="<?= $user['email']; ?>" class="img-40" alt="">
                                    <p class="d-inline-block m-l-20"><?= $user['email']; ?></p>
                                </div>
                            </td>
                            <td>
                                <!-- <p class="d-inline-block m-r-20">4 : 36</p>
                                <div class="progress d-inline-block">
                                    <div class="progress-bar bg-c-pink" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:60%">
                                    </div>
                                </div> -->
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="task-contain">
                                    <img src="<?= base_url('assets/img/tanggal.png'); ?>" data-toggle="tooltip" title="<?= date('d F Y', $user['date_created']); ?>" class="img-40" alt="">
                                    <p class="d-inline-block m-l-20"><?= date('d F Y', $user['date_created']); ?></p>
                                </div>
                            </td>
                            <td>
                                <!-- <p class="d-inline-block m-r-20">4 : 36</p>
                                <div class="progress d-inline-block">
                                    <div class="progress-bar bg-c-pink" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:60%">
                                    </div>
                                </div> -->
                            </td>
                        </tr>
                </table>
            </div>
        </div>
    </div>
</div>