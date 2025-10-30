export function highlightCurrentPage() {
    // เลือกปุ่ม Navigation ทั้งหมดที่มีคลาส 'nav-link'
    const navLinks = document.querySelectorAll(".nav-link");

    // รับ Pathname ปัจจุบัน
    const currentPathname = window.location.pathname;

    // ลบ active class ออกจากทุกปุ่มก่อนเสมอ
    navLinks.forEach(link => {
        link.classList.remove("active");
        link.removeAttribute("aria-current");
    });

    // ค้นหาและตั้งค่า active class ให้กับลิงก์ที่ตรงกัน
    let foundMatch = false;
    navLinks.forEach(link => {
        // สร้าง URL object เพื่อดึงแค่ pathname ที่ถูกต้อง
        const linkPathname = new URL(link.href).pathname;

        // ตรวจสอบแบบตรงตัวหรือตรวจสอบว่า pathname ของลิงก์เป็นส่วนหนึ่งของ pathname ปัจจุบัน
        if (currentPathname === linkPathname || currentPathname.startsWith(linkPathname + '/')) {
            link.classList.add("active");
            link.setAttribute("aria-current", "page");
            foundMatch = true;
        }
    });

    // ถ้าไม่มีการจับคู่แบบ 'startsWith' ให้ลองจับคู่แบบอื่นดู
    if (!foundMatch) {
        navLinks.forEach(link => {
            const linkPathname = new URL(link.href).pathname;
            if (currentPathname.includes(linkPathname) && linkPathname !== '/') {
                link.classList.add("active");
                link.setAttribute("aria-current", "page");
            }
        });
    }
}

// เรียกใช้ฟังก์ชันเมื่อหน้าเว็บโหลดเสร็จ
document.addEventListener("DOMContentLoaded", highlightCurrentPage);