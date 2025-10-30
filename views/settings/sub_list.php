<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การแจ้งเตือน</title>
</head>

<body class="ibm-plex-sans-thai-regular">
    <main>
        <!-- Top Nav -->
        <?php include('./views/components/topnav.php'); ?>
        <div id="contents-main" class="container">

            <div class="row justify-content-center align-items-center mt-3">
                <div class="col-sm-12 col-md-8 col-lg-6">
                    <?php
                    require_once('./views/components/back.php');
                    goBack('settings');
                    ?>
                </div>
            </div>

            <div class="row justify-content-center align-items-center mt-3">
                <div class="col-sm-12 col-md-8 col-lg-6">
                    <div class="card bg-sq-navy-white">
                        <div class="card-body">
                            <h5 class="m-0"><span class="material-symbols-outlined align-middle">notification_settings</span> การแจ้งเตือน</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center align-items-center mt-2">
                <div class="col-sm-12 col-md-8 col-lg-6">
                    <div class="card border-0 shadow">
                        <div class="card-body">
                            <div class="row justify-content-center align-items-center">
                                <div class="col-6">
                                    <h6 class="m-0">สถานะ : <small id="txtSub"></small></h6>
                                </div>
                                <div class="col-6 text-center">
                                    <button id="btSub" type="button" class="btn btn-warning w-100" style="display: none;"> <i class="material-icons align-middle">notification_add</i> <small>รับการแจ้งเตือน</small></button>
                                    <span id="txLoad"></span>
                                    <button id="btUnsub" type="button" class="btn btn-danger w-100" style="display: none;"> <i class="material-icons align-middle">notifications_off</i> <small>ยกเลิกการแจ้งเตือน</small></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center mt-2">
                <div class="col-sm-12 col-md-8 col-lg-6">
                    <table id="subTable" class="table table-sm table-bordered display responsive wrap" style="width: 100%;"></table>
                </div>
            </div>
        </div>
        <?php
        require_once('./views/components/back_m_nav.php');
        goBackMnav('settings');
        ?>
    </main>
</body>

</html>
<script type="module" src="assets/js/app.js" defer></script>
<script type="module" src="assets/js/table/subList.js" defer></script>