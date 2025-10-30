<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการเข้าสู่ระบบ</title>
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
                            <h5 class="m-0"><i class="fas fa-sign-in-alt"></i> ประวัติการเข้าสู่ระบบ</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mt-2">
                <div class="col-sm-12 col-md-8 col-lg-6">
                    <table id="tokenTable" class="table table-sm table-bordered border-sq-navy display responsive wrap" style="width: 100%";></table>
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
<script type="module" src="assets/js/table/tokenList.js" defer></script>