// This ONLY belong to login page.
import {
  togglePassword,
  validateInput,
  validateForm,
  btnLoading,
} from "./module/ui.js";
import Swal from "./module/sweetalert2.all.min+esm.js";

document.addEventListener("DOMContentLoaded", async () => {
  // Elements
  const loginForm = document.getElementById("loginForm");
  const btnLogin = document.getElementById("btnLogin");
  const username = document.getElementById("username");
  const password = document.getElementById("password");
  const BtnPass = document.getElementById("BtnPass");
  const cpDateText = document.getElementById("cpDate");

  // Date of copyright
  const cpYear = new Date().getFullYear();

  // Toggle password visibility
  BtnPass.addEventListener("click", () => {
    togglePassword("BtnPass", "password");
  });

  // Clear user image if exist
  if (localStorage.getItem("empImg")) {
    localStorage.removeItem("empImg");
  }

  // Vilidate input fields
  validateInput(loginForm);

  // Handle form submission
  loginForm.addEventListener("submit", async (event) => {
    event.preventDefault(); // Prevent default form submission

    btnLoading("กำลังเข้าสู่ระบบ...", true);

    // If the form is valid, you can proceed with form submission
    if (validateForm(loginForm)) {
      await fetch("auth/login", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          username: username.value,
          password: password.value,
        }),
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.json();
        })
        .then((data) => {
          if (data.status === "success") {
            Swal.fire({
              title: "เข้าสู่ระบบสำเร็จ",
              icon: "success",
              timer: 2000,
              timerProgressBar: true,
              text: "กำลังไปหน้าหลัก...",
              showConfirmButton: false,
              allowOutsideClick: false,
            }).then((result) => {
              if (result.dismiss === Swal.DismissReason.timer) {
                window.location.href = "home";
              }
            });
          }
        })
        .catch((error) => {
          Swal.fire({
            title: "รหัสพนักงานหรือรหัสผ่านไม่ถูกต้อง",
            icon: "error",
            text: "โปรดลองอีกครั้ง.",
            confirmButtonColor: "#3085d6",
            allowOutsideClick: false,
          });
          btnLoading("เข้าสู่ระบบ", false);
          loginForm.reset();
          loginForm.classList.add("was-validated");
        });
    } else {
      event.stopPropagation();
      btnLoading("เข้าสู่ระบบ", false);
      loginForm.reset();
      loginForm.classList.add("was-validated");
    }
  });
  // End of form submission handler

  // Date for footer
  cpDateText.textContent = `2025-${cpYear}`;
});
