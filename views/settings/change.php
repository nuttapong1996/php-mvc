<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เปลี่่ยนรหัสผ่าน</title>
</head>

<body class="ibm-plex-sans-thai-regular">
    <main>
        <!-- Top Nav -->
        <?php include('./views/components/topnav.php'); ?>
        <div id="contents-main" class="container">
            <div class="row justify-content-center mt-3">
                <div class="col-sm-12 col-md-8 col-lg-6">
                    <?php
                    require_once('./views/components/back.php');
                    goBack('settings');
                    ?>
                </div>
            </div>

            <div class="row justify-content-center mt-3">
                <div class="col-sm-12 col-md-8 col-lg-6">
                    <div class="card bg-sq-navy-white">
                        <div class="card-body">
                            <h5 class="m-0"><span class="fas fa-key"></span> เปลี่่ยนรหัสผ่าน</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mt-2">
                <div class="col-sm-12 col-md-6 col-lg-6">
                    <div class="card border-0 shadow">
                        <div class="card-body">
                            <form id="changeForm" class="needs-validation" novalidate>
                                <div class="row justify-content-center mb-3">
                                    <div class="col-sm-12 col-md-9 col-lg-10">
                                        <div class="input-group has-validation">
                                            <input type="password" name="OldPass" id="OldPass" required
                                                placeholder="รหัสผ่านปัจจุบัน" class="form-control">
                                            <button type="button" id="BtnOldPass"
                                                class="btn btn-outline-secondary border-secondary-subtle">
                                                <i class="bi bi-eye-slash"></i>
                                            </button>
                                            <div class="invalid-feedback" id="feedback">
                                               กรุณากรอกรหัสผ่านปัจจุบัน
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row justify-content-center mb-3">
                                    <div class="col-sm-12 col-md-9 col-lg-10">
                                        <div class="input-group has-validation">
                                            <input type="password" name="NewPass" id="NewPass" required
                                                placeholder="รหัสผ่านใหม่" class="form-control">
                                            <button type="button" id="BtnNewPass"
                                                class="btn btn-outline-secondary border-secondary-subtle">
                                                <i class="bi bi-eye-slash"></i>
                                            </button>
                                            <div class="invalid-feedback">
                                                กรุณากรอกรหัสผ่านใหม่
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center mb-2">
                                    <div class="col-sm-12 col-md-9 col-lg-10">
                                        <div class="input-group has-validation mb-3">
                                            <input type="password" name="cfPass" id="cfPass" required
                                                placeholder="ยืนยันรหัสผ่านใหม่"
                                                class="form-control form-control-lg fs-6 ">
                                            <button type="button" id="BtnCfPass"
                                                class="btn btn-outline-secondary border-secondary-subtle">
                                                <i class="bi bi-eye-slash"></i>
                                            </button>
                                            <div class="invalid-feedback">
                                                กรุณากรอกรหัสผ่านใหม่อีกครั้ง
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row justify-content-center">
                                    <div class="col-sm-12 col-md-9 col-lg-10">
                                        <button class="btn btn-sq-navy w-100 p-3 fs-6" type="submit"><i class="fas fa-check"></i> ตกลง</button>
                                    </div>
                                </div>
                            </form>
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
    <script type="module" defer src="assets/js/change.js">
    </script>
</body>

</html>