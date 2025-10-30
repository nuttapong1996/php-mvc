import Swal from "./module/sweetalert2.all.min+esm.js";
import {
  togglePassword,
  validatePassword,
  validateCfPassword,
  validateInput,
  validateForm,
  btnLoading,
} from "./module/ui.js";

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("registerForm");
  // const fName = document.getElementById('fName');
  const username = document.getElementById("username");
  // const userName = document.getElementById('userName');
  const password = document.getElementById("password");
  const cfPass = document.getElementById("cfPass");
  const IdenCode = document.getElementById("IdenCode");
  const email = document.getElementById("email");

  const BtnPass = document.getElementById("BtnPass");
  const BtnCfPass = document.getElementById("BtnCfPass");

  // Toggle password visibility for user password and confirm password fields
  BtnPass.addEventListener("click", () => {
    togglePassword("BtnPass", "password");
  });
  BtnCfPass.addEventListener("click", () => {
    togglePassword("BtnCfPass", "cfPass");
  });

  // Password Validation
  password.addEventListener("input", () => {
    validatePassword.call(password);
  });

  // Confirm Password Validation
  cfPass.addEventListener("input", () => {
    validateCfPassword.call(cfPass, password);
  });

  // Validate input fields
  validateInput(form);

  // Form submission
  form.addEventListener("submit", (event) => {
    event.preventDefault(); // Prevent default form submission

    btnLoading("กำลังสมัครสมาชิก...", true);

    if (validateForm(form)) {
      // If the form is valid, you can proceed with form submission
      fetch("auth/register", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          username: username.value,
          password: password.value,
          IdenCode: IdenCode.value,
          email: email.value,
        }),
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.json();
        })
        .then((data) => {
          if (data.status === "notfound") {
            if (data.title === "username") {
              Swal.fire({
                icon: "error",
                title: "ไม่พบรหัสพนักงาน",
                text: "กรุณาตรวจสอบข้อมูล",
                confirmButtonText: "ตกลง",
                allowOutsideClick: false,
              });
              btnLoading("สมัคร", false);
              form.reset();
              form.classList.add("was-validated")
            }
          }
          if (data.status === "exist") {
            if (data.title === "username") {
              Swal.fire({
                icon: "warning",
                title: "รหัสพนักงานนี้มีในระบบแล้ว",
                html: ` มีบัญชีอยู่แล้ว ?
                                        <a href="login" autofocus>เข้าสู่ระบบ</a>`,
                confirmButtonText: "ตกลง",
                allowOutsideClick: false,
              });
              btnLoading("สมัคร", false);
              form.reset();
              form.classList.add("was-validated")
            } else if (data.title === "idcard") {
              Swal.fire({
                icon: "warning",
                title: "รหัสบัตรประชาชนนี้ถูกใช้ไปแล้ว",
                text: "กรุณาติดต่อ จนท.ผู้ดูแล",
                confirmButtonText: "ตกลง",
                allowOutsideClick: false,
              });
              btnLoading("สมัคร", false);
              form.reset();
              form.classList.add("was-validated")
            } else if (data.title === "email") {
              Swal.fire({
                icon: "warning",
                title: "อีเมลนี้่ถูกใช้ไปแล้ว",
                text: "กรุณาเลือกอีเมลอื่น",
                confirmButtonText: "ตกลง",
                allowOutsideClick: false,
              });
              btnLoading("สมัคร", false);
              form.reset();
              form.classList.add("was-validated")
            }
          } else if (data.status === "success") {
            Swal.fire({
              icon: "success",
              title: "สมัครสมาชิกสำเร็จ",
              timer: 2000,
              timerProgressBar: true,
              text: "กำลังไปหน้า Login...",
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
          btnLoading("สมัคร", false);
          form.reset();
          form.classList.add("was-validated")
          // alert('Registration failed. Please try again.'); // Show error message
        });
    } else {
      event.stopPropagation();
      btnLoading("สมัคร", false);
      form.reset();
      form.classList.add("was-validated"); // Add validation class to the form
    }
  });
});
