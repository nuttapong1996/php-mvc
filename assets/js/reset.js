import {
  validateInput,
  validateForm,
  validatePassword,
  togglePassword,
  validateCfPassword,
  btnLoading,
} from "./module/ui.js";
import Swal from "./module/sweetalert2.all.min+esm.js";

document.addEventListener("DOMContentLoaded", () => {
  const resetForm = document.getElementById("resetForm");
  const NewPass = document.getElementById("NewPass");
  const cfPass = document.getElementById("cfPass");
  const BtnPass = document.getElementById("BtnPass");
  const BtnCfPass = document.getElementById("BtnCfPass");

  // fetch validatre Usercode and Reset Token.
  // แยก userCode และ resetToken จาก path
  const pathParts = window.location.pathname.split("/");
  const username = pathParts[3];
  const resetToken = pathParts[4];

  fetch("auth/checkreset", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      username: username,
      resetToken: resetToken,
    }),
  })
    .then((Checkreset) => {
      if (!Checkreset.ok) {
        throw new Error("Network response was not ok");
      }
      return Checkreset.json();
    })
    .then((res_check) => {
      if (res_check.status !== "valid") {
        Swal.fire({
          title: "โทเคนไม่ถูกต้อง",
          text: "กรุณาลองใหม่อีกครั้ง",
          icon: "error",
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true,
          allowOutsideClick: false,
        }).then((result) => {
          if (result.dismiss === Swal.DismissReason.timer) {
            window.location.href = "forgot";
          }
        });
      }
    });

  // Handdling form reset passsword

  BtnPass.addEventListener("click", () => {
    togglePassword("BtnPass", "NewPass");
  });

  BtnCfPass.addEventListener("click", () => {
    togglePassword("BtnCfPass", "cfPass");
  });

  NewPass.addEventListener("input", () => {
    validatePassword.call(NewPass);
  });

  cfPass.addEventListener("input", () => {
    validateCfPassword.call(cfPass, NewPass);
  });

  validateInput(resetForm);

  resetForm.addEventListener("submit", (e) => {
    e.preventDefault();
    btnLoading("กำลังรีเซ็ตรหัสผ่าน...", true);
    if (validateForm(resetForm)) {
      fetch("auth/reset", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          NewPass: NewPass.value,
          username: username,
        }),
      })
        .then((res) => {
          if (!res.ok) {
            throw new Error("Network response was not ok");
          }
          return res.json();
        })
        .then((data) => {
          if (data.status === "success") {
            Swal.fire({
              icon: "success",
              title: "รีเซ็ตรหัสผ่านสำเร็จ",
              text: "กำลังกลับไปหน้า Login...",
              timer: 2000,
              timerProgressBar: true,
              showConfirmButton: false,
              allowOutsideClick: false,
            }).then((result) => {
              if (result.dismiss === Swal.DismissReason.timer) {
                window.location.href = "login";
              }
            });
          }
        })
        .catch((error) => {
          btnLoading("ตกลง", false);
          resetForm.reset();
          resetForm.classList.add("was-validated");
        });
    } else {
      e.stopPropagation();
      btnLoading("ตกลง", false);
      resetForm.reset();
      resetForm.classList.add("was-validated");
    }
  });
});
