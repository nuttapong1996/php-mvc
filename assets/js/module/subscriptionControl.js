import Swal from './sweetalert2.all.min+esm.js';
import { getCsrfToken } from './utils.js';

// Function เช็คการสมัครก่ารแจ้งเตืน
export async function checksub() {
    const registration = await navigator.serviceWorker.ready;
    const subscription = await registration.pushManager.getSubscription();

    if (!subscription) {
        return false;
    }

    const response = await fetch('push/get-sub', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({ endpoint: subscription.endpoint })
    });

    const data = await response.json();

    if (data.status === 'sub') {
        return true;
    } else {
        await subscription.unsubscribe();
        return false;
    }
}


// Function สมัครการแจ้งเตือน
export async function enableNotif() {
    try {
        const csrf = getCsrfToken();

        // ดึง public key
        const pubRes = await fetch('getpub', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        });
        const { publicKey } = await pubRes.json();

        // ขอ permission
        const permission = await Notification.requestPermission();
        if (permission !== 'granted') return;

        // รอ service worker พร้อม
        const sw = await navigator.serviceWorker.ready;

        // subscribe
        const subscription = await sw.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: publicKey
        });

        // ส่ง subscription ไป backend
        const res = await fetch('push/add-sub', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify(subscription)
        });

        if (!res.ok) throw new Error('HTTP error ' + res.status);

        const data = await res.json();

        if (data.status === 'success') {
            const notiIcon = '<i class="fa-solid fa-bell"></i>';
            await Swal.fire({
                title: 'การแจ้งเตือน',
                text: 'รับการแจ้งเตือนเรียบร้อยแล้ว',
                icon: 'success',
                iconHtml: notiIcon,
                customClass: {
                    icon: 'fa-beat',
                },
                showConfirmButton: false,
                allowOutsideClick: false,
                timerProgressBar: true,
                timer: 3000,
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.timer) {
                    window.location.reload();
                }
            });
        }
    } catch (err) {
        console.error('Enable notification error:', err);
    }
}
// Function ยกเลิกสมัครการแจ้งเตือน
export async function disableNotif() {

    // Create variable (registration) and assign subscription's value from recurent Service Worker.
    const registration = await navigator.serviceWorker.ready;

    // Create variable (subscription) and assign value from variable (registration) .
    const subscription = await registration.pushManager.getSubscription();

    const endPoint = subscription.endpoint;

    try {
        const csrfToken = getCsrfToken();

        // 🔑 unsubscribe จาก browser
        const unsubscribed = await subscription.unsubscribe();
        if (!unsubscribed) {
            console.warn("Failed to unsubscribe from browser.");
        }

        const unsub = await fetch('push/un-sub', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                endpoint: endPoint
            })
        });

        if (!unsub.ok) {
            throw new Error('HTTP error ' + unsub.status);
        }

        const data = await unsub.json();
        const notiIcon = '<i class="fa-solid fa-bell-slash"></i>';

        if (data.status === 'success') {
            await Swal.fire({
                title: 'ยกเลิกการแจ้งเตือน',
                text: 'ยกเลิกการแจ้งเตือนเรียบร้อยแล้ว',
                icon: 'warning',
                iconHtml: notiIcon,
                customClass: {
                    icon: 'fa-beat',
                },
                showConfirmButton: false,
                allowOutsideClick: false,
                timerProgressBar: true,
                timer: 3000,
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.timer) {
                    window.location.reload();
                }
            })
        }
    } catch (error) {
        console.error('Fetch error:', error);
    }
}
// Function ลบรายการการสมัครสมาชิก
export async function deleteNotif(subCode) {

    try {
        const csrfToken = getCsrfToken();

        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.getSubscription();

        if (subscription) {
            const unsubscribed = await subscription.unsubscribe();
            if (!unsubscribed) {
                console.warn("Failed to unsubscribe from browser.");
            }
        } else {
            console.warn("No active subscription found.");
        }

        const unsub = await fetch('push/del-sub', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                subCode: subCode
            })
        });

        if (!unsub.ok) {
            throw new Error('HTTP error ' + unsub.status);
        }

        const data = await unsub.json();
        const notiIcon = '<i class="fa-solid fa-bell-slash"></i>';

        if (data.status === 'success') {
            await Swal.fire({
                title: 'ยกเลิกการแจ้งเตือน',
                text: 'ยกเลิกการแจ้งเตือนเรียบร้อยแล้ว',
                icon: 'warning',
                iconHtml: notiIcon,
                customClass: {
                    icon: 'fa-beat',
                },
                showConfirmButton: false,
                allowOutsideClick: false,
                timerProgressBar: true,
                timer: 3000,
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.timer) {
                    window.location.reload();
                }
            });
        }
    } catch (error) {
        console.error('Fetch error:', error);
    }
}
// Function แสดงสถานะการรับการแจ้งเตือนในหน้า main หรือ อื่นๆ 
export async function subDisplay(btnSub, txSub) {
    const SubBtn = document.getElementById(btnSub);
    const subStatus = document.getElementById(txSub);

    if (SubBtn && subStatus) {

        const subscribe = await checksub();

        if (subscribe == true) {
            SubBtn.style.display = "none";
            subStatus.style.display = "block";
        } else {
            SubBtn.style.display = "block";
            subStatus.style.display = "none";
        }
        //Assign event to Subscription Button.
        SubBtn.addEventListener('click', async () => {

            SubBtn.style.display = "none";
            await enableNotif();
            subStatus.style.display = "block";
        });
    }
}
// Function แสดงสถานะการรับการแจ้งเตือนหน้ ตั้งค่าการแจ้งเตือน 
export async function subToggle(btnSub, btnUnsub, txSub, txLoad) {
    const SubBtn = document.getElementById(btnSub);
    const UnsubBtn = document.getElementById(btnUnsub);
    const subStatus = document.getElementById(txSub);
    const loading = document.getElementById(txLoad);


    if (SubBtn && UnsubBtn && subStatus) {

        const subscribe = await checksub();

        if (subscribe == true) {
            SubBtn.style.display = "none";
            UnsubBtn.style.display = "block";
            subStatus.innerHTML = "ลงทะเบียนแล้ว"
            subStatus.classList.remove("text-danger");
            subStatus.classList.add("text-success");
        } else if (subscribe == false) {
            SubBtn.style.display = "block";
            UnsubBtn.style.display = "none";
            subStatus.innerHTML = "ไม่ได้ลงทะเบียน"
            subStatus.classList.remove("text-success");
            subStatus.classList.add("text-danger");
        }


        SubBtn.addEventListener('click', async () => {
            SubBtn.style.display = "none";
            loading.innerText = "กำลังลงทะเบียน..."
            loading.style.display = "block";

            try {
                await enableNotif();  // รอจนเสร็จจริง ๆ
            } catch (err) {
                console.error(err);
            } finally {
                loading.style.display = "none";
            }
        });

        UnsubBtn.addEventListener('click', async () => {
            UnsubBtn.style.display = "none";
            loading.innerText = "กำลังยกเลิก..."
            loading.style.display = "block";

            try {
                await disableNotif();
            } catch (error) {
                console.error(error);
            } finally {
                loading.style.display = "none";
            }

        });
    }
}

