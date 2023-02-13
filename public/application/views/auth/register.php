<style>
    .common-img-bg {
        background-size: cover;
        background: url("<?= base_url(); ?>assets/images/logo/background.jpg") no-repeat center center fixed;
        height: 100%;
    }
</style>


<!-- Pre-loader end -->
<section class="login p-fixed d-flex text-center bg-primary common-img-bg">
    <!-- Container-fluid starts -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <!-- Authentication card start -->
                <div class="signup-card card-block auth-body mr-auto ml-auto">
                    <!-- <form class="md-float-material"> -->
                    <div class="text-center">
                        <img src="<?= base_url('assets/images/logo/auth-header.png'); ?>" alt="logo.png">
                    </div>
                    <div class="auth-box">
                        <div class="row m-b-20">
                            <div class="col-md-12">
                                <h3 class="text-center txt-primary text-center">Sign up. It is fast and easy.</h3>
                            </div>
                        </div>
                        <?php
                        $regionals = $this->db->get('Region')->result();
                        ?>
                        <hr />
                        <form method="post" action="<?= base_url('auth/register'); ?>">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Choose Username" name="username" value="<?= set_value('username'); ?>">
                                <?= form_error('username', '<small class="text-danger float-left mb-3 ">', '</small>'); ?>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Your Email Address" name="email" value="<?= set_value('email'); ?>">
                                <?= form_error('email', '<small class="text-danger float-left mb-3  ">', '</small>'); ?>
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" placeholder="Choose Password" name="password">
                                <?= form_error('password', '<small class="text-danger float-left mb-3  ">', '</small>'); ?>
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" placeholder="Confirm Password" name="password1">

                            </div>
                            <!-- <div class="form-group">
                                 <select id="id_role" class="form-control" name="id_role">
                                     <option value="">Choose Role user</option>
                                     <?php foreach ($id_role as $role) : ?>
                                         <option value="<?= $role->id; ?>"><?= $role->role; ?></option>
                                     <?php endforeach; ?>
                                 </select>
                             </div> -->
                            <!-- <div class="form-group">
                                 <select id="regional" class="form-control" name="id_regional">
                                     <option value="">Choose Regional</option>
                                     <?php foreach ($regionals as $regional) : ?>
                                         <option value="<?= $regional->id_region; ?>"><?= $regional->region_name; ?></option>
                                     <?php endforeach; ?>
                                 </select>
                             </div> -->
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="No Hp" name="no_hp">
                                <?= form_error('no_hp', '<small class="text-danger float-left mb-3  ">', '</small>'); ?>
                            </div>
                            <div class="row m-t-30">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">Sign up now.</button>
                                </div>
                            </div>
                        </form>
                        <hr />
                        <div class="row">
                            <div class="col-md-9">
                                <p class="text-inverse text-left m-b-0">Already have an account? </p>
                                <?php if (!$this->session->userdata('email')) : ?>
                                    <a href="<?= base_url('auth'); ?>">
                                        <p class="text-left text-primary"><b>Back to login</b></p>
                                    </a>
                                <?php else : ?>
                                    <a href="<?= base_url('Admin/user'); ?>">
                                        <p class="text-left text-primary"><b>Back to user page</b></p>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-3">
                                <img src="<?= base_url('assets/images/logo/logo-adhi.png'); ?>" alt="small-logo.png">
                            </div>
                        </div>
                    </div>
                    <!-- </form> -->
                    <!-- end of form -->
                </div>
                <!-- Authentication card end -->
            </div>
            <!-- end of col-sm-12 -->
        </div>
        <!-- end of row -->
    </div>
    <!-- end of container-fluid -->
</section>