<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลส่วนตัว</title>
</head>

<body class="ibm-plex-sans-thai-regular">
    <main>
        <!-- Top Nav -->
        <?php include('./views/components/topnav.php'); ?>
        <div id="contents-main" class="container">

            <div class="row mt-3 justify-content-center align-items-center">
                <div class="col-sm-12 col-md-12 col-lg-7">
                    <?php include('./views/components/backhome.php') ?>
                </div>
            </div>

            <div class="row justify-content-center align-items-center mt-3 mb-3">
                <div class="col-sm-12 col-md-12 col-lg-7">
                    <div class="card bg-sq-navy-white border-0 shadow mb-2">
                        <div class="card-body">
                            <h5 class="mb-0 ">
                                <i class="material-icons align-middle">account_box</i>
                                ข้อมูลส่วนตัว
                            </h5>
                        </div>
                    </div>
                    <div class="card bg-sq-logo border-0 shadow">
                        <div class="card-body">
                            <!-- <div class="row text-center">
                                <div class="col-sm-12 col-md-6 col-lg-4">
                                    <h6 class="text-sq-navy">
                                        <span id="txEmpcode" class="empInfo">Loading...</span>
                                    </h6>
                                </div>
                            </div> -->

                            <div class="row align-items-center">
                                <!-- <div class="col-sm-12 col-md-6 col-lg-4 pb-3">
                                    <div style="overflow:hidden;" class="text-center">
                                        <img id="empImg" alt="Employee" src="assets/icons/avatar.png"
                                            style="width:150px; height:150px; object-fit:cover;" class="rounded-circle mb-3">
                                    </div>
                                </div> -->
                                <div id="empInfo" class="col-sm-12 col-md-6 col-lg-7 text-sq-navy">
                                    <div class="text-center text-md-start">
                                        <h4 class="empInfo fw-bold"><span id="txEmpname">Loading...</span></h4>
                                        <!-- <hr class="empInfo my-2">
                                        <h6 class="empInfo fw-normal"><span id="txPosition">Loading...</span></h6>
                                        <h6 class="empInfo fw-normal"><span id="txDept">Loading...</span></h6>
                                        <h6 class="empInfo fw-normal">
                                            <span id="txNat">Loading...</span>
                                            <span id="txPjt">Loading...</span> -->
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mt-3">
                <div class="col-sm-12 col-md-12 col-lg-7">
                    <div class="card border-0 shadow">
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <a href="menu1" class="list-group-item list-group-item-action p-3 fw-medium">
                                    <i class="material-icons align-middle">menu</i>
                                    <span>menu-1</span>
                                    <i class="bi bi-chevron-right float-end fs-3"></i>
                                </a>
                                <a href="menu2" class="list-group-item list-group-item-action p-3 fw-medium ">
                                    <i class="material-icons align-middle">menu</i>
                                    <span>menu-2</span>
                                    <i class="bi bi-chevron-right float-end fs-3"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('./views/user/components/m_nav_personal.php'); ?>
    </main>
</body>
</html>