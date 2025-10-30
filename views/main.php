<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP MVC Template</title>
</head>

<body class="ibm-plex-sans-thai-regular">
    <main>
        <!-- Top Nav -->
        <?php include('components/topnav.php'); ?>
        <div id="contents-main" class="container">
            <div class="container mt-3 mb-2 ">
                <div class="row justify-content-center align-items-center">
                    <div class="col-sm-12 col-md-8 col-lg-6">
                        <div class="card  bg-sq-navy-white border-0 shadow mb-2">
                            <div class="card-body">
                                <div class="justify-content-between d-flex align-items-center">
                                    <h5 class="mb-0 ">ผู้ใช้ (User)</h5>
                                    <button id="btnSub" class="btn btn-warning" style="display: none;">
                                        <i class="material-icons align-middle">
                                            notification_add
                                        </i>
                                        <small>รับการแจ้งเตือน</small>
                                    </button>
                                    <span id="txSub" class="badge rounded-pill text-bg-warning" style="display: none;">
                                        <small class='material-icons align-middle'>notifications</small>
                                        <small>รับการแจ้งเตือนแล้ว</small>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card bg-sq-logo border-0 shadow">
                            <div class="card-body">
                                <div class="row justify-content-center align-items-center">
                                    <div class="col-12 text-end">
                                        <a href="settings" class="text-dark-emphasis fs-5"><i
                                                class="bi bi-gear-fill"></i></a>
                                    </div>
                                </div>

                                <div class="row align-items-center">
                                    <!-- <div class="col-sm-12 col-md-6 col-lg-4">
                                        <div style="overflow:hidden;" class="text-center border-black">
                                            <img id="empImg" alt="Employee" src="assets/icons/avatar.png"
                                                style="width:150px; height:150px; object-fit:cover;" class="rounded-circle mb-3">
                                        </div>
                                    </div> -->
                                    <div class="col-sm-12 col-md-6 col-lg-6 text-sq-navy">
                                        <div class="text-center text-md-start ">
                                            <h4 class="empInfo"><span id="txEmpname">Loading...</span></h4>
                                            <!-- <h6 class="text-sq-navy"><span id="txEmpcode" class="empInfo">Loading...</span></h6>
                                            <h6 class="empInfo fw-normal"><span id="txPosition">Loading...</span></h6>
                                            <h6 class="empInfo fw-normal"><span id="txDept">Loading...</span></h6> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <a href="personal" class="text-decoration-none fw-medium text-sq-navy"><span>ดูโปรไฟล์
                                            <i class="bi bi-chevron-right"></i></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="container">
                <div class="row justify-content-center align-items-center">
                    <div class="col-sm-12 col-md-8 col-lg-6">

                        <div class="card bg-sq-navy-white border-0 mb-2">
                            <div class="card-body">
                                <h5 class="mb-0 ">บริการ (Services)</h5>
                            </div>
                        </div>

                        <div class="d-grid gap-2 text-center" style="grid-template-columns: 1fr 1fr 1fr;">

                            <a href="menu1" class="btn btn-sq-navy-outline  p-3">
                                <i class="material-icons align-middle fs-1">menu</i><br>
                                <small>Menu 1</small>
                            </a>
                            <a href="menu2" class="btn btn-sq-navy-outline  p-3">
                                <i class="material-icons align-middle fs-1">menu</i><br>
                                <small>Menu 2</small>
                            </a>
                            <a href="menu3" class="btn btn-sq-navy-outline  p-3">
                                <i class="material-icons align-middle fs-1">menu</i><br>
                                <small>Menu 3</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('components/m_nav_main.php'); ?>
    </main>
</body>

</html>