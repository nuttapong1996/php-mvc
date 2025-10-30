<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การตั้งค่า</title>
</head>

<body class="ibm-plex-sans-thai-regular">
    <main>
        <!-- Top Nav -->
        <?php include('./views/components/topnav.php'); ?>
        <div id="contents-main" class="container">
            <div class="row mt-3 justify-content-center align-items-center">
                <div class="col-sm-12 col-md-8 col-lg-6">
                    <?php include('./views/components/backhome.php') ?>
                </div>
            </div>

            <div class="row justify-content-center mt-3">
                <div class="col-sm-12 col-md-8 col-lg-6">
                    <div class="card border-0 shadow  bg-sq-navy-white">
                        <div class="card-body">
                            <h5 class="m-0 fw-bold "><i class="fas fa-cog"></i> การตั้งค่า</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mt-2">
                <div class="col-sm-12 col-md-8 col-lg-6">
                    <div class="card border-0 shadow">
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <a href="settings/noti" class="list-group-item list-group-item-action text-sq-navy p-3">
                                    <span><i class="fas fa-bell"></i> การแจ้งเตือน</span>
                                    <i class="fas fa-chevron-right float-end fs-5"></i>
                                </a>
                                <a href="settings/password" class="list-group-item list-group-item-action text-sq-navy p-3">
                                    <span><i class="fas fa-key"></i> เปลี่ยนรหัสผ่าน</span>
                                    <i class="fas fa-chevron-right float-end fs-5"></i>
                                </a>
                                <a href="settings/token" class="list-group-item list-group-item-action text-sq-navy p-3">
                                    <span><i class="fas fa-sign-in-alt"></i> ประวัติการเข้าสู่ระบบ</span>
                                    <i class="fas fa-chevron-right float-end fs-5"></i>
                                </a>
                                <a href="settings/api-token" class="list-group-item list-group-item-action text-sq-navy p-3">
                                    <span> <i class="fas fa-code"></i> API Token</span>
                                    <i class="fas fa-chevron-right float-end fs-5"></i>
                                </a>
                                <button id="btnLogout" class="list-group-item list-group-item-action text-danger p-3">
                                    <span> <i class="fas fa-sign-out-alt"></i> ออกจากระบบ (Logout)</span>
                                    <i class="fas fa-sign-out-alt float-end fs-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('./views/settings/components/m_nav.php'); ?>
    </main>
</body>

</html>
<script type="module" src="assets/js/logout.js" defer></script>