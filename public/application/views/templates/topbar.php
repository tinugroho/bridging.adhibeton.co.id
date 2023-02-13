<style>
    .pcoded .pcoded-header .navbar-logo[logo-theme="theme1"] {
        background: rgb(115, 105, 159);
        background: linear-gradient(90deg, rgba(115, 105, 159, 1) 31%, rgba(79, 70, 93, 1) 65%);
    }
</style>

<!-- Pre-loader start -->
<div class="theme-loader">
    <div class="ball-scale">
        <div class='contain'>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">

                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
        </div>
    </div>
</div>
<!-- Pre-loader end -->
<div id="pcoded" class="pcoded">
    <div class="pcoded-overlay-box"></div>
    <div class="pcoded-container navbar-wrapper">

        <nav class="navbar header-navbar pcoded-header">
            <div class="navbar-wrapper">

                <div class="navbar-logo">
                    <a class="mobile-menu" id="mobile-collapse" href="#!">
                        <i class="ti-menu"></i>
                    </a>
                    <a class="mobile-search morphsearch-search" href="#">
                        <i class="ti-search"></i>
                    </a>
                    <a href="<?= base_url(); ?>">
                        <img class="img-fluid" src="<?= base_url(); ?>assets/images/logo/bg-topbar.png" alt="Theme-Logo" />
                    </a>
                    <a class="mobile-options">
                        <i class="ti-more"></i>
                    </a>
                </div>

                <div class="navbar-container container-fluid">
                    <ul class="nav-left">
                        <li>
                            <div class="sidebar_toggle"><a href="javascript:void(0)"><i class="ti-menu"></i></a></div>
                        </li>

                        <li>
                            <a href="#!" onclick="javascript:toggleFullScreen()">
                                <i class="ti-fullscreen"></i>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav-right">
                        <li class="header-notification">
                        </li>
                        <li class="user-profile header-notification">
                            <a href="#!">
                                <img src="<?= base_url('assets/img/') . $user['image']; ?>" class="img-radius" alt="User-Profile-Image">
                                <span><?= $user['username']; ?></span>
                                <i class="ti-angle-down"></i>
                            </a>
                            <ul class="show-notification profile-notification">
                                <li>
                                    <a href="" data-toggle="modal" data-target="#viewProfile"><i class="ti-user"></i>View Profile</a>
                                </li>
                                <li>
                                    <a href="" data-toggle="modal" data-target="#setting"><i class="ti-settings"></i>Settings</a>
                                </li>
                                <li>
                                    <a href="" data-toggle="modal" data-target="#gantiPassword"><i class="ti-unlock"></i>Change Password</a>
                                </li>
                                <li>
                                    <a href="<?= base_url('auth/logout'); ?>">
                                        <i class="ti-layout-sidebar-left"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>

                </div>
            </div>
        </nav>