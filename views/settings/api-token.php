<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Token</title>
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
                            <h5 class="m-0"><i class="fas fa-code"></i> API Token</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center align-items-center mt-2">
                <div class="col-sm-12 col-md-8 col-lg-6">
                    <div class="card border-0 shadow">
                        <div class="card-body">
                            <div class="row justify-content-center align-items-center p-2">
                                <span id="noticeToken" class="text-danger text-center fw-bold mb-2" style="display: none;"> Token นี้จะแสดงเพียงครั้งเดียว <br> กรุณาคัดลอก Token ของคุณ</span>
                                <div id="divToken" class="input-group mb-2" style="display: none;">
                                    <span class="input-group-text bg-sq-navy-white border-sq-navy">Token</span>
                                    <input id="txToken" type="text" class="form-control border-sq-navy" readonly>
                                    <button id="btnCopy" class="btn btn-sq-navy-outline"><i class="fas fa-copy"></i></button>
                                </div>
                                <button id="btnCreate" class="btn btn-lg btn-success w-100" style="display: none;"><i class="fas fa-plus-square"></i> สร้าง</button>
                                    <table id="tokenTable" class="table table-bordered" style="display: none;">
                                    <thead>
                                        <tr class="align-middle">
                                            <th>สร้างเมื่อ</th>
                                            <th>ใช้งานล่าสุด</th>
                                            <th>ใช้ไป/ครั้ง</th>
                                            <th class="text-center">ลบ</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tokenTbody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mt-5">
                <div class="col-sm-12 col-md-8 col-lg-6">
                    <div aria-live="polite" aria-atomic="true" class="d-flex justify-content-center align-items-center w-100">
                        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="toast-body">
                                Token copied
                            </div>
                        </div>
                    </div>
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
<script type="module" src="assets/js/apiToken.js" defer></script>