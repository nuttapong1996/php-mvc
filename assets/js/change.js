import { validateInput, validateForm, validatePassword, validateCfPassword, togglePassword } from "./module/ui.js";
import { getCsrfToken} from "./module/utils.js";
import Swal from './module/sweetalert2.all.min+esm.js';


document.addEventListener('DOMContentLoaded', () => {

    const changeForm = document.getElementById('changeForm');
    const OldPass = document.getElementById('OldPass');
    const NewPass = document.getElementById('NewPass');
    const cfPass = document.getElementById('cfPass');

    const BtnOldPass = document.getElementById('BtnOldPass');
    const BtnNewPass = document.getElementById('BtnNewPass');
    const BtnCfPass = document.getElementById('BtnCfPass');

    const csrfToken = getCsrfToken();

    // Validate Input 
    validateInput(changeForm);

    // Password Input Event 
    OldPass.addEventListener('change', () => {
        checkpass.call(OldPass ,csrfToken);
    });

    OldPass.addEventListener('input', () => {
        validatePassword.call(OldPass);
    });

    NewPass.addEventListener('input', () => {
        validatePassword.call(NewPass);
    });

    cfPass.addEventListener('input', () => {
        validateCfPassword.call(cfPass, NewPass);
    });

    // Password Button Event 
    BtnOldPass.addEventListener('click', () => {
        togglePassword('BtnOldPass', 'OldPass')
    });
    BtnNewPass.addEventListener('click', () => {
        togglePassword('BtnNewPass', 'NewPass')
    });
    BtnCfPass.addEventListener('click', () => {
        togglePassword('BtnCfPass', 'cfPass')
    });


    changeForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const InputNewPass = document.getElementById('NewPass');
        if (validateForm(changeForm)) {

            fetch('auth/change', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken 
                },
                body: JSON.stringify({
                    NewPass: InputNewPass.value
                })
            })
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'เปลี่ยนรหัสผ่านสำเร็จ',
                            text: data.message,
                            icon: 'success',
                            allowOutsideClick: false,
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            text: "โปรดเข้าสู่ระบบใหม่อีกครั้ง",
                        })
                            .then((result) => {
                                if (result.dismiss === Swal.DismissReason.timer) {
                                    window.location.href = 'login';
                                }
                            })
                    } else {
                        Swal.fire({
                            title: 'เปลี่ยนรหัสผ่านไม่สำเร็จ',
                            text: 'กรุณาลองใหม่อีกครั้ง',
                            icon: 'error',
                            confirmButtonText: 'ตกลง',
                            allowOutsideClick: false,
                        })
                    }
                })
                .catch(err => {
                    console.log(err);
                })
        } else {
            changeForm.classList.add('was-validated');
            e.stopPropagation();
        }
    });
});





function checkpass(csrfToken) {
    // const value = 
    const feedback = document.getElementById('feedback');
    let message = '';

    fetch('auth/checkpass', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken // แนบ token
        },
        body: JSON.stringify({
            OldPass: this.value
        })
    })
        .then(res => {
            return res.json();
        })
        .then(data => {
            if (data.status === 'valid') {
                this.classList.add('is-valid');
                message = 'รหัสผ่านถูกต้อง';
                feedback.classList.remove('invalid-feedback');
                feedback.classList.add('valid-feedback');
                feedback.textContent = message;
            } else if (data.status === 'invalid') {
                this.classList.add('is-invalid');
                message = 'รหัสผ่านไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง';
                feedback.classList.remove('valid-feedback');
                feedback.classList.add('invalid-feedback');
                feedback.textContent = message;
            }
        })
        .catch(err => {
            console.log(err);
        })
}