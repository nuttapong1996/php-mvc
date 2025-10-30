<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_SESSION['title']; ?></title>
</head>

<body class="ibm-plex-sans-thai-regular">
    <main>
        <!-- Top Nav -->
        <?php include('components/topnav.php'); ?>
        <div id="contents-main" class="container-fluid">
            <div class="row justify-content-center align-items-center h-100">
                <div class="col-sm-12 col-md-8 col-lg-4">
                    <div class="card border-0 shadow">
                        <div class="card-header bg-sq-navy-white border-sq-navy">
                            <h5 class="m-0 text-center">
                                <i class="material-icons align-middle"> <?php echo $_SESSION['icon']; ?></i>
                                <?php echo ' ' . $_SESSION['title']; ?>
                            </h5>
                        </div>
                        <div class="card-body p-5">
                            <h5 class="card-title text-center text-sq-navy mb-3">
                                กรุณาใส่รหัสผ่าน
                            </h5>
                            <form id="unlockForm" class="needs-validation" novalidate>
                                <div class="input-group has-validation">
                                    <input type="password" name="input_lock" id="input_lock" class="form-control form-control-lg border-sq-navy text-sq-navy" required autofocus>
                                    <button class="btn btn-sq-navy-outline"
                                        type="button" id="BtnPass">
                                        <i class="bi bi-eye-slash"></i>
                                    </button>
                                    <div id="input_feedback" class="invalid-feedback">
                                        กรุณากรอกรหัสผ่าน
                                    </div>
                                </div>
                                <div class="mb-4">
                                </div>
                                <button type="submit" class="btn btn-lg btn-sq-navy w-100">
                                    <i class="material-icons align-middle">lock</i>
                                    ปลดล็อก
                                </button>
                                <div class="mt-5 text-center">
                                    <a href="home" class="btn btn-sq-navy-outline "><i class="fas fa-home"></i> กลับหน้าหลัก</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>
<script type="module" src="assets/js/unlock.js" defer></script>