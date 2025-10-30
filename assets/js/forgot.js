import { validateInput, validateForm , btnLoading } from "./module/ui.js";
import Swal from "./module/sweetalert2.all.min+esm.js"
document.addEventListener('DOMContentLoaded', () => {

    const forgotForm = document.getElementById('forgotForm');
    const username = document.getElementById('username');
    const IdenCode = document.getElementById('IdenCode');

    validateInput(forgotForm);


    forgotForm.addEventListener('submit', (e) => {
        e.preventDefault();

        btnLoading('กำลังโหลด...', true);

        if (validateForm(forgotForm)) {
            fetch('auth/forgot', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    username: username.value,
                    IdenCode: IdenCode.value
                })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'รหัสพนักงานและเลขบัตรประชาชนถูกต้อง',
                            text: data.message,
                            icon: 'success',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                            text: "กำลังโหลดหน้ารีเซ็ตรหัสผ่าน...",

                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.timer) {
                                window.location.href = `reset/${username.value}/${data.data.resetToken}`;
                            }
                        });
                    } else if (data.status === 'error') {
                        Swal.fire({
                            title: 'รหัสพนักงานและเลขบัตรประชาชนไม่ถูกต้อง',
                            text: 'โปรดลองอีกครั้ง',
                            icon: 'error',
                            confirmButtonText: 'ตกลง',
                            allowOutsideClick: false
                        });
                        forgotForm.classList.add('was-validated');
                        forgotForm.reset();
                        btnLoading('ตกลง', false);
                    
                    }
                })
        } else {            
            forgotForm.classList.add('was-validated');
            btnLoading('ตกลง', false);
            e.stopPropagation();
        }
    });

});