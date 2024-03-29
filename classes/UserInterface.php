<?php

class UserInterface
{
    static function siteURL() {
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ||
            $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        return $protocol.$domainName;
    }

    static function printHead() {
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';
        if (isset($_SESSION['handle'])) {
            if($_SESSION['handle'] == CONFIG_GLOBAL_ADMIN) {
                error_reporting(E_ALL); 
                ini_set('display_errors', '1'); 
            }
        }
        echo '
            <head>
                <meta charset="utf-8" />
                <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
                <meta name="description" content="" />
                <meta name="author" content="John Pieroni" />
                
                <meta property="og:title" content="Vendor Holonet" />
                <meta property="og:description" content="Browse Vendors and their wares from one source!" />
                <meta property="og:image" content="'.UserInterface::siteURL().'/app/assets/img/logo-large.png" />
                
                <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
                <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
                <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
                <link rel="manifest" href="/site.webmanifest">
                <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
                <meta name="msapplication-TileColor" content="#da532c">
                <meta name="theme-color" content="#ffffff">
                <title>Vendor Holonet</title>
                <!-- Bootstrap 5 CSS --> <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
                <!-- JQuery 3 + DataTables CSS --> <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.13.1/af-2.5.1/b-2.3.3/cr-1.6.1/date-1.2.0/fc-4.2.1/fh-3.3.1/kt-2.8.0/r-2.4.0/rg-1.3.0/rr-1.3.1/sc-2.0.7/sb-1.4.0/sp-2.1.0/sl-1.5.0/sr-1.2.0/datatables.min.css"/>
                <!-- Default CSS --> <link href="'.UserInterface::siteURL().'/app/css/styles.css" rel="stylesheet" />
                <!-- FontAwesome --> <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
            </head>
        ';
    }

    static function printNav() {
        $admin = '';
        if($_SESSION['handle'] == CONFIG_GLOBAL_ADMIN) {
            $admin = '<li><a class="dropdown-item" href="'.UserInterface::siteURL().'/app/admin">Admin Panel</a></li>';
        }

        echo '
            <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
                <!-- Navbar Brand-->
                <a class="navbar-brand ps-3" href="'.UserInterface::siteURL().'/app/index.php">Vendor Holonet</a>
                <!-- Sidebar Toggle-->
                <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
 
                <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                    <div class="input-group">
                        <!--<input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                        <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>-->
                    </div>
                </form>
            
                <!-- Navbar-->
                <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-gear"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            '.$admin.'
                            <li><a class="dropdown-item" href="'.UserInterface::siteURL().'/index.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        ';
    }

    static function printSideNav() {
        echo '
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div style="width: 100%; text-align: center;"><img width="100" height="100" src="'.UserInterface::siteURL().'/app/assets/img/logo-large.png"></div>
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Core</div>
                            <a class="nav-link" href="'.UserInterface::siteURL().'/app/index.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-shop"></i></div>
                                Malls
                            </a>
                            
                            <div class="sb-sidenav-menu-heading">Search</div>
                            <a class="nav-link" href="'.UserInterface::siteURL().'/app/vendors.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-user-tie"></i></div>
                                Vendors
                            </a>
                            <a class="nav-link" href="'.UserInterface::siteURL().'/app/wares.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                                Wares
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        '.$_SESSION['handle'].'<br/>'.$_SESSION['location_str'].'
                    </div>
                </nav>
            </div>
        ';
    }

    static function printFooter() {
        echo '
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Developed by Cedron Tryonel. Vendor Holonet is a website associated with <a target="_blank" href="https://www.swcombine.com">Star Wars Combine</a>.</div>
                        <div>
                            <a target="_blank" href="https://github.com/johnpieroni89/vendorholonet">GitHub Repo</a>
                        </div>
                    </div>
                </div>
            </footer>
        ';
    }

    static function printScripts() {
        echo '
            <!-- Bootstrap 5 JS --> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
            <!-- JQuery 3 + DataTables JS --> <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.13.1/af-2.5.1/b-2.3.3/cr-1.6.1/date-1.2.0/fc-4.2.1/fh-3.3.1/kt-2.8.0/r-2.4.0/rg-1.3.0/rr-1.3.1/sc-2.0.7/sb-1.4.0/sp-2.1.0/sl-1.5.0/sr-1.2.0/datatables.min.js"></script> <!-- https://datatables.net/examples/advanced_init/dom_multiple_elements.html -->
            <!-- Default JS --> <script type="text/javascript" src="'.UserInterface::siteURL().'/app/js/scripts.js"></script>
            <!-- Chart.js --> <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script> <!-- https://www.w3schools.com/js/js_graphics_chartjs.asp -->
        ';
    }
}