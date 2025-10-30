import Swal from "../module/sweetalert2.all.min+esm.js";
import { date_inter_format } from "../module/dateFormat.js";
import { getCsrfToken } from "../module/utils.js";

document.addEventListener("DOMContentLoaded", async () => {
  const tokenTable = document.getElementById("tokenTable");
  const csrfToken = getCsrfToken();

  try {
    const tokenRes = await fetch("data/tokenlist", {
      method: "GET",
      credentials: "include",
      headers: {
        "Content-Type": "application/json",
      },
    });

    if (!tokenRes.ok) {
      throw new Error("HTTP error " + tokenRes.status);
    }

    const tokenData = await tokenRes.json();

    const data = tokenData.data;
    const dataTable = new DataTable(tokenTable, {
      destroy: true,
      data: data,
      columns: [
        {
          data: null,
          title: "No.",
          render: function (data, type, row, meta) {
            return meta.row + 1;
          },
        },
        {
          data: "device_name",
          title: "อุปกรณ์",
          className: "align-middle",
        },
        {
          data: "create_at",
          title: "เข้าใช้",
          className: "align-middle",
          render: function (data) {
            const date = new Date(data);
            const options = {
              year: "numeric",
              month: "short",
              day: "numeric",
              hour: "numeric",
              minute: "numeric",
              second: "numeric",
            };
            return date.toLocaleString("en-US", options);
          },
        },
        {
          data: "expires_at",
          title: "หมดอายุ",
          render: function (data) {
            const date = new Date(data);
            const options = {
              year: "numeric",
              month: "short",
              day: "numeric",
              hour: "numeric",
              minute: "numeric",
              second: "numeric",
            };
            return date.toLocaleString("en-US", options);
          },
        },
        {
          data: null,
          title: "จัดการ",
          className: "text-center align-middle",
          render: (data, type, row) => {
            if (row.remark === "(Current Device)") {
              return "<span class='badge bg-success'>อุปกรณ์ปัจจุบัน</span>";
            } else {
              return `<button class='btnLogout btn btn-danger btn-sm' data-id='${row.token_id}' data-device='${row.device_name}' data-ip='${row.ip_address}'><i class='fa-solid fa-right-from-bracket'></i> Logout</button>`;
            }
          },
        },
      ],
      responsive: true,
      ordering: false,
      autoWidth: true,
      bLengthChange: false,
      pageLength: 5,
      searching: false,
      pagingType: "simple",
      language: {
        url: "https://cdn.datatables.net/plug-ins/2.0.1/i18n/th.json",
        emptyTable: "ไม่มีข้อมูล",
      },
      layout: {
        topStart: null,
        topEnd: null,
        bottomStart: "paging",
        bottomEnd: "info",
      },
      columnDefs: [
        { width: "50px", targets: 0 }, // col เลขที่ใบเตือน
        { width: "200px", targets: 1 }, // col วันที่
        { width: "200px", targets: 2 }, // col ระดับการเตือน
        { width: "200px", targets: 3 }, // col เหตุผล
        { width: "100px", targets: 4 }, // col หมายเหตุ
      ],
      initComplete: function () {
        $(tokenTable).find("thead th").css({
          "background-color": "#224788",
          color: "white",
          borderColor: " white",
          verticalAlign: "middle",
        });
      },
    });
  } catch (error) {
    console.error("Fetch error:", error);
  }

  tokenTable.addEventListener("click", async (e) => {
    if (e.target.closest(".btnLogout")) {
      const btn = e.target.closest(".btnLogout");
      const tokenId = btn.dataset.id;
      const deviceName = btn.dataset.device;
      const ipAddr = btn.dataset.ip;

      const result = await Swal.fire({
        title: "ลบอุปกรณ์",
        text: `ต้องการลบอุปกรณ์ :${deviceName}\nIP: ${ipAddr} ?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        allowOutsideClick: false,
      });

      if (result.isConfirmed) {
        await logOut(tokenId);
        Swal.fire({
          title: "ลบอุปกรณ์สำเร็จ",
          text: "ลบอุปกรณ์เรียบร้อยแล้ว",
          icon: "success",
          timer: 2000,
          showConfirmButton: false,
          allowOutsideClick: false,
        }).then((result) => {
          if (result.dismiss === Swal.DismissReason.timer) {
            window.location.reload();
          }
        });
      }
    }
  });
});

async function logOut(tokenId) {
  const csrfToken = getCsrfToken();

  try {
    const response = await fetch("auth/rmtoken", {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrfToken,
      },
      body: JSON.stringify({
        tokenid: tokenId,
      }),
    });

    if (!response.ok) {
      throw new Error("HTTP Error" + response.status);
    }

    const data = await response.json();
    return data;
  } catch (error) {
    console.error("Fetch error:", error);
  }
}
