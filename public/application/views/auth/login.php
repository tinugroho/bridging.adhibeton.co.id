<style>
    .common-img-bg {
        background-size: cover;
        background: url("<?= base_url(); ?>assets/images/logo/background.jpg") no-repeat center center fixed;
        height: 100%;
    }
</style>

<section class="login p-fixed d-flex text-center bg-primary common-img-bg">
    <!-- Container-fluid starts -->
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <!-- Authentication card start -->
                <div class="login-card card-block auth-body mr-auto ml-auto">
                    <!-- <form class="md-float-material"> -->
                    <div class="text-center">
                        <img src="<?= base_url(); ?>assets/images/logo/auth-header.png" alt="logo.png">
                    </div>
                    <div class="auth-box">
                        <div class="row m-b-20">
                            <div class="col-md-12">
                                <h3 class="text-left txt-primary text-center">Sign In</h3>
                            </div>
                        </div>
                        <?= $this->session->flashdata('pesan'); ?>
                        <hr />
                        <form method="post" action="<?= base_url('auth') . (!empty($r) ? '?r=' . $r : ''); ?>">
                            <div class="form-group">
                                <input type="email" class="form-control" placeholder="Your Email Address" name="email" value="<?= set_value('email'); ?>">
                                <?= form_error('email', '<small class="text-danger float-left mb-3  ">', '</small>'); ?>
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" placeholder="Password" id="password" name="password">
                                <?= form_error('password', '<small class="text-danger float-left  ">', '</small>'); ?>
                            </div>
                            <div class="row m-t-25 text-left">
                                <div class="col-sm-7 col-xs-12">
                                    <div class="checkbox-fade fade-in-primary">
                                        <label>
                                            <input type="checkbox" value="" id="checkbox">
                                            <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                            <span class="text-inverse">Show Password</span>
                                        </label>
                                    </div>
                                </div>
                                <!-- <div class="col-sm-5 col-xs-12 forgot-phone text-right">
                                    <a href="auth-reset-password.html" class="text-right f-w-600 text-inverse"> Forgot Your Password?</a>
                                </div> -->
                            </div>
                            <div class="row m-t-30">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">Sign in</button>
                                </div>
                            </div>
                            <!-- <div class="row">
                                <div class="col-md-12">
                                    <a href="<?= base_url('auth/register'); ?>" class="btn btn-secondary btn-md btn-block waves-effect text-center m-b-20">Register</a>
                                </div>
                            </div> -->
                        </form>
                        <hr />
                        <div class="row">
                            <div class="col-md-9">
                                <p class="text-inverse text-left m-b-0">Thank you and enjoy our website.</p>
                                <p class="text-inverse text-left"><b>Your Authentication Team</b></p>
                            </div>
                            <div class="col-md-3">
                                <img src="<?= base_url(); ?>assets/images/logo/logo-adhi.png" alt="small-logo.png">
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