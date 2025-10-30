import { togglePassword, validateForm, validateInput , btnLoading} from './module/ui.js';
import { getCsrfToken } from './module/utils.js';


const sectionName = window.location.pathname.replace('/', ''); // ดึง section จาก URL
const unlockForm = document.getElementById('unlockForm');
const inputLock = document.getElementById('input_lock');
const input_feedback = document.getElementById('input_feedback');
const BtnPass = document.getElementById('BtnPass');
const csrfToken = getCsrfToken();

// Toggle password visibility
BtnPass.addEventListener('click', () => { togglePassword('BtnPass', 'input_lock'); });

// Vilidate input fields
validateInput(unlockForm);

unlockForm.addEventListener('submit', async function (e) {
    e.preventDefault(); // ป้องกัน form submit ปกติ
    btnLoading('  <i class="material-icons align-middle">lock_open</i> กำลังปลดล็อก..',true);
    if (validateForm(unlockForm)) {
        const response = await fetch(`/${sectionName}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': csrfToken // แนบ token
            },
            body: JSON.stringify({
                password: inputLock.value
            })
        });

        if(!response.ok){
            return;
        }

        const data = await response.json();

        if (data.status === 'success' && data.redirect) {
            window.location.href = data.redirect;
        } else {
            btnLoading('  <i class="material-icons align-middle">lock</i> ปลดล็อก',false);
            input_feedback.textContent = 'รหัสผ่านไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง';
            inputLock.classList.add('is-invalid');
            inputLock.setCustomValidity('รหัสผ่านไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
        }
    } else {
        e.stopPropagation();
         btnLoading('  <i class="material-icons align-middle">lock</i> ปลดล็อก',false);
        unlockForm.classList.add('was-validated');
    }
});

