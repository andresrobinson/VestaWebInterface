<?php

session_start();

    if (file_exists( '../includes/config.php' )) { require( '../includes/config.php'); }  else { header( 'Location: ../install' );};

    if(base64_decode($_SESSION['loggedin']) == 'true') {}
      else { header('Location: ../login.php'); }

    $requestdns = $_GET['domain'];

    if (isset($requestdns) && $requestdns != '') {}
      else { header('Location: ../list/dns.php'); }

    $postvars = array(
      array('user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-user','arg1' => $username,'arg2' => 'json'),
      array('user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-dns-domain','arg1' => $username,'arg2' => $requestdns, 'arg3' => 'json'),
      array('user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-dns-templates','arg1' => 'json'));

    $curl0 = curl_init();
    $curl1 = curl_init();
    $curl2 = curl_init();
    $curlstart = 0; 

    while($curlstart <= 2) {
        curl_setopt(${'curl' . $curlstart}, CURLOPT_URL, $vst_url);
        curl_setopt(${'curl' . $curlstart}, CURLOPT_RETURNTRANSFER,true);
        curl_setopt(${'curl' . $curlstart}, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt(${'curl' . $curlstart}, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt(${'curl' . $curlstart}, CURLOPT_POST, true);
        curl_setopt(${'curl' . $curlstart}, CURLOPT_POSTFIELDS, http_build_query($postvars[$curlstart]));
        $curlstart++;
    } 

    $admindata = json_decode(curl_exec($curl0), true)[$username];
    $useremail = $admindata['CONTACT'];
    $dnsname = array_keys(json_decode(curl_exec($curl1), true));
    $dnsdata = array_values(json_decode(curl_exec($curl1), true));
    $dnstpl = array_values(json_decode(curl_exec($curl2), true));
    if ($dnsname[0] == '') { header('Location: ../list/dns.php'); }
    if(isset($admindata['LANGUAGE'])){ $locale = $countries[$admindata['LANGUAGE']]; }
    setlocale(LC_CTYPE, $locale); setlocale(LC_MESSAGES, $locale);
    bindtextdomain('messages', 'locale');
    textdomain('messages');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/ico" href="../plugins/images/favicon.ico">
    <title><?php echo $sitetitle; ?> - DNS</title>
    <link href="../bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css" rel="stylesheet">
    <link href="../plugins/bower_components/footable/css/footable.bootstrap.css" rel="stylesheet">
    <link href="../plugins/bower_components/bootstrap-select/bootstrap-select.min.css" rel="stylesheet">
    <link href="../css/animate.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link href="../plugins/bower_components/toast-master/css/jquery.toast.css" rel="stylesheet">
    <link href="../css/colors/<?php if(isset($_SESSION['theme'])) { echo base64_decode($_SESSION['theme']); } else {echo $themecolor; } ?>" id="theme" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.5/sweetalert2.min.css" />
    <!--[if lt IE 9]>
       <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
       <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body class="fix-header">
    <!-- ============================================================== -->
    <!-- Preloader -->
    <!-- ============================================================== -->
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> 
        </svg>
    </div>
    <div id="wrapper">
        <nav class="navbar navbar-default navbar-static-top m-b-0">
            <div class="navbar-header">
                <div class="top-left-part">
                    <!-- Logo -->
                    <a class="logo" href="../index.php">
                        <!-- Logo icon image, you can use font-icon also --><b>
                        <!--This is dark logo icon--><img src="../plugins/images/admin-logo.png" alt="home" class="logo-1 dark-logo" /><!--This is light logo icon--><img src="../plugins/images/admin-logo-dark.png" alt="home" class="logo-1 light-logo" />
                     </b>
                        <!-- Logo text image you can use text also --><span class="hidden-xs">
                        <!--This is dark logo text--><img src="../plugins/images/admin-text.png" alt="home" class="hidden-xs dark-logo" /><!--This is light logo text--><img src="../plugins/images/admin-text-dark.png" alt="home" class="hidden-xs light-logo" />
                     </span> </a>
                </div>
                <!-- /Logo -->
                <!-- Search input and Toggle icon -->
                <ul class="nav navbar-top-links navbar-left">
                    <li><a href="javascript:void(0)" class="open-close waves-effect waves-light visible-xs"><i class="ti-close ti-menu"></i></a></li>      
                </ul>
                <ul class="nav navbar-top-links navbar-right pull-right">

                    <li class="dropdown">
                        <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#"><b class="hidden-xs"><?php print_r($uname); ?></b><span class="caret"></span> </a>
                        <ul class="dropdown-menu dropdown-user animated flipInY">
                            <li>
                                <div class="dw-user-box">
                                    <div class="u-text">
                                        <h4><?php print_r($uname); ?></h4>
                                        <p class="text-muted"><?php print_r($useremail); ?></p></div>
                                </div>
                            </li>
                            <li role="separator" class="divider"></li>
                            <li><a href="../profile.php"><i class="ti-home"></i> My Account</a></li>
                            <li><a href="../profile.php?settings=open"><i class="ti-settings"></i> Account Setting</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="../process/logout.php"><i class="fa fa-power-off"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav slimscrollsidebar">
                <div class="sidebar-head">
                    <h3>
                        <span class="fa-fw open-close">
                            <i class="ti-menu hidden-xs"></i>
                            <i class="ti-close visible-xs"></i>
                        </span> 
                        <span class="hide-menu">Navigation</span>
                    </h3>  
                </div>
               <ul class="nav" id="side-menu">
                            <li> 
                                <a href="../index.php" class="waves-effect">
                                    <i class="mdi mdi-home fa-fw"></i> <span class="hide-menu">Dashboard</span>
                                </a> 
                            </li>

                            <li class="devider"></li>
                            <li>
                                <a href="#" class="waves-effect"><i  class="ti-user fa-fw"></i><span class="hide-menu"> <?php print_r($uname); ?><span class="fa arrow"></span></span>
                                </a>
                                <ul class="nav nav-second-level collapse" aria-expanded="false" style="height: 0px;">
                                    <li> <a href="../profile.php"><i class="ti-home fa-fw"></i> <span class="hide-menu"> My Account</span></a></li>
                                    <li> <a href="../profile.php?settings=open"><i class="ti-settings fa-fw"></i> <span class="hide-menu"> Account Setting</span></a></li>
                                </ul>
                            </li>
                            <?php if ($webenabled == 'true' || $dnsenabled == 'true' || $mailenabled == 'true' || $dbenabled == 'true') { echo '<li class="devider"></li>
                                <li class="active"> <a href="#" class="waves-effect"><i class="mdi mdi-av-timer fa-fw" data-icon="v"></i> <span class="hide-menu">Management <span class="fa arrow"></span> </span></a>
                                    <ul class="nav nav-second-level">'; } ?>
                            <?php if ($webenabled == 'true') { echo '<li> <a href="../list/web.php"><i class="ti-world fa-fw"></i><span class="hide-menu">Web</span></a> </li>'; } ?>
                            <?php if ($dnsenabled == 'true') { echo '<li> <a href="../list/dns.php" class="active"><i class="fa fa-sitemap fa-fw"></i><span class="hide-menu">DNS</span></a> </li>'; } ?>
                            <?php if ($mailenabled == 'true') { echo '<li> <a href="../list/mail.php"><i class="fa fa-envelope fa-fw"></i><span class="hide-menu">Mail</span></a> </li>'; } ?>
                            <?php if ($dbenabled == 'true') { echo '<li> <a href="../list/db.php"><i class="fa fa-database fa-fw"></i><span class="hide-menu">Database</span></a> </li>'; } ?>
                            <?php if ($webenabled == 'true' || $dnsenabled == 'true' || $mailenabled == 'true' || $dbenabled == 'true') { echo '</ul>
                                </li>'; } ?>
                            <li> <a href="../list/cron.php" class="waves-effect"><i  class="mdi mdi-settings fa-fw"></i> <span class="hide-menu">Cron Jobs</span></a> </li>
                            <li> <a href="../list/backups.php" class="waves-effect"><i  class="fa fa-cloud-upload fa-fw"></i> <span class="hide-menu">Backups</span></a> </li>
                            <?php if ($ftpurl == '' && $webmailurl == '' && $phpmyadmin == '' && $phppgadmin == '') {} else { echo '<li class="devider"></li>
                                <li><a href="#" class="waves-effect"><i class="mdi mdi-apps fa-fw"></i> <span class="hide-menu">Apps<span class="fa arrow"></span></span></a>
                                    <ul class="nav nav-second-level">'; } ?>
                            <?php if ($ftpurl != '') { echo '<li><a href="' . $ftpurl . '" target="_blank"><i class="fa fa-file-code-o fa-fw"></i><span class="hide-menu">FTP</span></a></li>';} ?>
                            <?php if ($webmailurl != '') { echo '<li><a href="' . $webmailurl . '" target="_blank"><i class="fa fa-envelope-o fa-fw"></i><span class="hide-menu">Webmail</span></a></li>';} ?>
                            <?php if ($phpmyadmin != '') { echo '<li><a href="' . $phpmyadmin . '" target="_blank"><i class="fa fa-edit fa-fw"></i><span class="hide-menu">phpMyAdmin</span></a></li>';} ?>
                            <?php if ($phppgadmin != '') { echo '<li><a href="' . $phppgadmin . '" target="_blank"><i class="fa fa-edit fa-fw"></i><span class="hide-menu">phpPgAdmin</span></a></li>';} ?>
                            <?php if ($ftpurl == '' && $webmailurl == '' && $phpmyadmin == '' && $phppgadmin == '') {} else { echo '</ul></li>';} ?>
                            <li class="devider"></li>
                            <li><a href="process/logout.php" class="waves-effect"><i class="mdi mdi-logout fa-fw"></i> <span class="hide-menu">Log out</span></a></li>
                            <?php if ($oldcpurl == '' || $supporturl == '') {} else { echo '<li class="devider"></li>'; } ?>
                            <?php if ($oldcpurl != '') { echo '<li><a href="' . $oldcpurl . '" class="waves-effect"> <i class="fa fa-tachometer fa-fw"></i> <span class="hide-menu"> Control Panel v1</span></a></li>'; } ?>
                            <?php if ($supporturl != '') { echo '<li><a href="' . $supporturl . '" class="waves-effect" target="_blank"> <i class="fa fa-life-ring fa-fw"></i> <span class="hide-menu">Support</span></a></li>'; } ?>
                        </ul>
            </div>
        </div>
        <div id="page-wrapper">
           <div class="container-fluid">
                <div class="row bg-title">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title">Edit DNS Domain</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="panel"> 
                            <div class="sk-chat-widgets">
                                <div class="panel panel-themecolor">
                                    <div class="panel-heading">
                                        <center>DOMAIN</center>
                                    </div>
                                    <div class="panel-body">
                                      <center><h2><?php print_r($dnsname[0]); ?></h2></center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="panel">
                            <div class="sk-chat-widgets">
                                <div class="panel panel-themecolor">
                                    <div class="panel-heading">
                                        <center>CREATED</center>
                                    </div>
                                    <div class="panel-body">
                                        <center>
                                            <h2>
                                                <?php $date=date_create($dnsdata[0]['DATE'] . ' ' . $dnsdata[0]['TIME']); echo date_format($date,"F j, Y - g:i A"); ?>
                                            </h2>
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="panel">
                            <div class="sk-chat-widgets">
                                <div class="panel panel-themecolor">
                                    <div class="panel-heading">
                                        <center>STATUS</center>
                                    </div>
                                    <div class="panel-body">
                                        <center>
                                            <h2>
                                                <?php if ($dnsdata[0]['SUSPENDED'] == 'no') {echo 'Active';} else {echo 'Suspended';}?>
                                            </h2>
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="white-box">
                            <form class="form-horizontal form-material" autocomplete="off" method="post" action="../change/dns.php">
                                <div class="form-group">
                                    <label class="col-md-12">Domain</label>
                                    <div class="col-md-12">
                                        <input type="text" disabled value="<? print_r($dnsname[0]); ?>" style="background-color: #eee;padding-left: 0.6%;border-radius: 2px;border: 1px solid rgba(120, 130, 140, 0.13);bottom: 19px;background-image: none;"class="form-control uneditable-input form-control-static"> 
                                        <input type="hidden" name="v_domain" value="<? print_r($dnsname[0]); ?>"> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="col-md-12">IP Address</label>
                                    <div class="col-md-12">
                                        <input type="text" name="v_ip" value="<? print_r($dnsdata[0]['IP']); ?>" class="form-control form-control-line" name="email" id="email"> </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">DNS Template</label>
                                    <div class="col-md-12">
                                        <select class="form-control select2" name="v_tpl" id="select2">
                                            <?php
                                                    if($dnstpl[0] != '') {
                                                        $x1 = 0; 

                                                        do {
                                                            echo '<option value="' . $dnstpl[$x1] . '">' . $dnstpl[$x1] . '</option>';
                                                            $x1++;
                                                        } while ($dnstpl[$x1] != ''); }

                                                ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="col-md-12">Expiration Date</label>

                                    <div class="col-md-12">
                                        <div class="input-group date">
                                            <input type="text" class="form-control datepicker" name="v_exp" value="<?php echo date("m/d/Y", strtotime($dnsdata[0]['EXP'])); ?>">
                                            <span class="input-group-addon">
                                                <i class="icon-calender">
                                                </i>
                                            </span> 
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label for="email" class="col-md-12">SOA (Start of Authority)</label>
                                    <div class="col-md-12">
                                        <input type="text" name="v_soa" value="<? print_r($dnsdata[0]['SOA']); ?>" class="form-control form-control-line" name="email" id="email"> </div>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="col-md-12">TTL (Time To Live)</label>
                                    <div class="col-md-12">
                                        <input type="text" name="v_ttl" value="<? print_r($dnsdata[0]['TTL']); ?>" class="form-control form-control-line" name="email" id="email"> </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button class="btn btn-success">Update Domain</button> &nbsp;
                                            <a href="../list/dns.php" style="color: inherit;text-decoration: inherit;"><button class="btn btn-muted" type="button">Back</button></a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
           <footer class="footer text-center">&copy; Copyright <?php echo date("Y") . ' ' . $sitetitle; ?>. All Rights Reserved. Vesta Web Interface <?php require '../includes/versioncheck.php'; ?> by CDG Web Services.</footer>
    </div>
    </div>
    <script src="../plugins/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="../plugins/bower_components/toast-master/js/jquery.toast.js"></script>
    <script src="../bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="../plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js"></script>
    <script src="../js/jquery.slimscroll.js"></script>
    <script src="../js/waves.js"></script>
    <script src="../plugins/bower_components/moment/moment.js"></script>
    <script src="../plugins/bower_components/footable/js/footable.min.js"></script>
    <script src="../plugins/bower_components/bootstrap-select/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="../plugins/bower_components/custom-select/custom-select.min.js"></script>
    <script src="../js/footable-init.js"></script>
    <script src="../js/custom.js"></script>
    <script src="../js/dashboard1.js"></script>
    <script src="../js/cbpFWTabs.js"></script>
    <script src="../plugins/bower_components/styleswitcher/jQuery.style.switcher.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.5/sweetalert2.all.js"></script>
    <script src="../plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript">
$('.datepicker').datepicker();
        (function () {
                [].slice.call(document.querySelectorAll('.sttabs')).forEach(function (el) {
                new CBPFWTabs(el);
            });
        })();
        document.getElementById('select2').value = '<? print_r($dnsdata[0]['TPL']); ?>'; 
        jQuery(function($){
            $('.footable').footable();
        });
        function confirmDelete(e){
            e1 = String(e)
            swal({
                title: 'Delete Web Domain:<br>' + e1 +' ?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then(function () {
                swal({
                    title: 'Processing',
                    text: '',
                    timer: 5000,
                    onOpen: function () {
                        swal.showLoading()
                    }
                }).then(
                    function () {},
                    // handling the promise rejection
                    function (dismiss) {
                        if (dismiss === 'timer') {
                            console.log('I was closed by the timer')
                        }
                    }
                )
                $.ajax({  
                    type: "GET",  
                    url: "../delete/domain.php",  
                    data: { 'domain':e1, 'verified':'yes' },
                    success:  function(){ window.location = "web.php?delcode=0"; },
                    error:  function(){ window.location = "web.php?delcode=0"; }
                })
            })}

        <?php if($_GET['delcode'] == "0"){ echo "swal({title:'Successfully Deleted!', type:'success'});";} ?>
    </script>
</body>

</html>