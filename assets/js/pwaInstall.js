import Swal from "./module/sweetalert2.all.min+esm.js";

document.addEventListener("DOMContentLoaded", () => {
  const bannerPcAndroid = document.getElementById("pcAndroid");
  const bannerIos = document.getElementById("iOSbaner");
  const deviceType = getDeviceType();

  let installed = false;

  // ตรวจสอบว่าแอปถูกติดตั้งอยู่หรือไม่
  if (window.matchMedia("(display-mode: standalone)").matches) {
    installed = true;
  } else if (window.navigator.standalone === true) {
    // iOS
    installed = true;
  }

  // แสดงหรือซ่อนปุ่มตามเงื่อนไข
  if (installed) {
    bannerPcAndroid.style.display = "none";
    bannerIos.style.display = "none";
  } else {
    if (deviceType === "android" || deviceType === "pc") {
      bannerPcAndroid.style.display = "block";
    } else if (deviceType === "ios") {
      bannerIos.style.display = "block";
    }
  }
});

// ฟังก์ชั่นปุ่มติดตั้ง
let deferredPrompt;

window.addEventListener("beforeinstallprompt", (e) => {
  e.preventDefault();
  deferredPrompt = e;

  const installButton = document.getElementById("installButton");

  installButton.addEventListener("click", async () => {
    installButton.disabled = true;
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    if (outcome === "accepted") {
      Swal.fire({
        title: "ติดตั้งสำเร็จ",
        text: "แอพฯถูกติดตั้งเรียบร้อยแล้ว",
        icon: "success",
        confirmButtonText: "ตกลง",
        confirmButtonColor: "#3085d6",
        allowOutsideClick: false,
      }).then((result)=>{
        if(result.isConfirmed){
            window.location.reload();
        }
      });
    } else {
      installButton.disabled = false;
    }
    deferredPrompt = null;
  });
});

function getDeviceType() {
  const ua = navigator.userAgent.toLowerCase();
  if (/android/.test(ua)) return "android";
  if (/iphone|ipad|ipod/.test(ua)) return "ios";
  if (/windows|macintosh|linux/.test(ua)) return "pc";
  return "unknown";
}
