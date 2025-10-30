export function empPic(ImgInput) {
    const empImg = document.getElementById(ImgInput);
    const iconPath = 'assets/icons/avatar.png';
    const cacheKey = 'empImg';
    const missingKey = 'empImgMissing';

    if (!empImg) return;

    const cacheData = localStorage.getItem(cacheKey);
    const missingFlag = localStorage.getItem(missingKey);


    // เพิ่ม transition สำหรับ fade-in
    empImg.style.transition = 'opacity 0.5s ease-in-out';

    // แสดง cache หรือ placeholder ก่อน
    empImg.src = cacheData || iconPath;
    empImg.alt = cacheData ? "รูปโปรไฟล์ (cache)" : "กำลังโหลดรูป...";
    empImg.style.opacity = '1'; // แสดงทันที

    // ตรวจสอบ server แบบ async background
    fetch('user/pic', { method: "GET", credentials: "include" })
        .then(res => {
            if (!res.ok) {
                localStorage.setItem(missingKey, 'true');
                return null;
            }
            return res.blob();
        })
        .then(blob => new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onloadend = () => resolve(reader.result);
            reader.onerror = reject;
            reader.readAsDataURL(blob);
        }))
        .then(base64data => {
            localStorage.setItem(cacheKey, base64data);
            localStorage.removeItem(missingKey); // ลบ flag เพราะมีรูปแล้ว
            empImg.src = base64data;
            empImg.alt = "รูปโปรไฟล์";
        })
        .catch((err) => {
            return null;
        });
}
