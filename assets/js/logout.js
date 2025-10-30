import Swal from "./module/sweetalert2.all.min+esm.js";

const btnLogout = document.getElementById("btnLogout");

btnLogout.addEventListener("click", async () => {
  const result = await Swal.fire({
    title: "ออกจากระบบ",
    text: "คุณต้องการออกจากระบบหรือไม่",
    icon: "info",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "ใช่",
    cancelButtonText: "ไม่ใช่",
    allowOutsideClick: false,
  });

  if (result.isConfirmed) {
    const logoutResult = await logout(); // รอให้ logout เสร็จ
    if (logoutResult?.code === 200) {
      await Swal.fire({
        title: "ออกจากระบบสำเร็จ",
        icon: "success",
        timer: 2000,
        timerProgressBar: true,
        text: "กำลังไปหน้า Login...",
        showConfirmButton: false,
        allowOutsideClick: false,
      });
      localStorage.removeItem("empImg");
      window.location.href = "login";
    } else {
      Swal.fire("ผิดพลาด", "ไม่สามารถออกจากระบบได้", "error");
    }
  }
});


async function logout() {
  try {
    const resLogout = await fetch("auth/logout", {
      method: "GET",
      credentials: "include",
      headers: {
        "Content-Type": "application/json",
      },
    });
    if (!resLogout.ok) {
      throw new Error("Network response was not ok");
    }
    const data = await resLogout.json(); // รอผล JSON
    return data;
  } catch (error) {
    console.error("There was a problem with the fetch operation:", error);
  }
}
