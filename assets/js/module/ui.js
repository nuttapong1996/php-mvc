export function togglePassword(passBtn, passField) {
  const passwordField = document.getElementById(passField);
  const icon = document.querySelector(`#${passBtn} i`);

  if (passwordField.type === "password") {
    passwordField.type = "text";
    icon.classList.remove("bi-eye-slash");
    icon.classList.add("bi-eye");
  } else {
    passwordField.type = "password";
    icon.classList.remove("bi-eye");
    icon.classList.add("bi-eye-slash");
  }
}

export function validatePassword() {
  const value = this.value;
  const feedback =
    this.closest(".input-group").querySelector(".invalid-feedback");
  let message = "";

  if (value.length < 8) {
    message = "รหัสผ่านควรมีความยาวอย่างน้อย 8 ตัวอักษร";
    this.classList.add("is-invalid");
  } else if (!/[A-Z]/.test(value)) {
    message = "รหัสผ่านควรมีอักษรพิมพ์ใหญ่อย่างน้อย 1 ตัว";
    this.classList.add("is-invalid");
  } else if (!/[a-z]/.test(value)) {
    message = "รหัสผ่านควรมีอักษรพิมพ์เล็กอย่างน้อย 1 ตัว";
    this.classList.add("is-invalid");
  } else if (!/[0-9]/.test(value)) {
    message = "รหัสผ่านควรมีตัวเลขอย่างน้อย 1 ตัว";
    this.classList.add("is-invalid");
  } else if (!/[!@#$%^&*(),.?":{}|<>]/.test(value)) {
    message = "รหัสผ่านควรมีตัวอักษรพิเศษอย่างน้อย 1 ตัว";
    this.classList.add("is-invalid");
  } else {
    this.classList.remove("is-invalid");
    return;
  }

  if (message) {
    this.classList.add("is-invalid");
    feedback.textContent = message;
    this.setCustomValidity(message);
  } else {
    this.classList.remove("is-invalid");
    feedback.textContent = "";
    this.setCustomValidity("");
  }
}

export function validateCfPassword(passField) {
  const value = this.value;
  const feedback =
    this.closest(".input-group").querySelector(".invalid-feedback");
  if (value !== passField.value) {
    feedback.textContent = "รหัสผ่านไม่ตรงกัน กรุณาลองใหม่อีกครั้ง";
    this.classList.add("is-invalid");
    this.setCustomValidity("รหัสผ่านไม่ตรงกัน กรุณาลองใหม่อีกครั้ง");
  } else {
    feedback.textContent = "รหัสผ่านตรงกัน";
    this.classList.remove("is-invalid");
    this.setCustomValidity("");
  }
}

export function validateInput(form) {
  const inputs = form.querySelectorAll("input[required]");
  const submitBtn = form.querySelector('button[type="submit"]');

  inputs.forEach((input) => {
    input.addEventListener("input", () => {
      if (input.checkValidity()) {
        input.classList.remove("is-invalid");
        input.classList.add("is-valid");
      } else {
        input.classList.add("is-invalid");
        input.setCustomValidity("");
      }
      // ตรวจสอบทุกช่อง
      const allValid = Array.from(inputs).every((i) => i.checkValidity());
      submitBtn.disabled = !allValid;
    });
  });
}

export function validateForm(form) {
  const inputs = form.querySelectorAll("input[required]");
  let isValid = null;

  inputs.forEach((input) => {
    if (!input.checkValidity()) {
      input.classList.add("is-invalid");
      isValid = false;
    } else {
      input.classList.remove("is-invalid");
      isValid = true;
    }
  });

  return isValid;
}

export function btnLoading(msg , status) {
  document.querySelector('button[type="submit"]').innerHTML = msg;
  document.querySelector('button[type="submit"]').disabled = status;
}
