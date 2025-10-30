import Swal from "../module/sweetalert2.all.min+esm.js";
import { subToggle, deleteNotif } from "../module/subscriptionControl.js";

document.addEventListener("DOMContentLoaded", async () => {
  subToggle("btSub", "btUnsub", "txtSub", "txLoad");

  const subTable = document.getElementById("subTable");

  const registration = await navigator.serviceWorker.ready;
  const subscription = await registration.pushManager.getSubscription();

  try {
    const listResponse = await fetch("push/usersub-list", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    });

    if (!listResponse.ok) {
      throw new Error("HTTP error " + listResponse.status);
    }

    const listData = await listResponse.json();

    const data = listData.data;
    const dataTable = new DataTable(subTable, {
      destroy: true,
      data: data,
      columns: [
        {
          data: null,
          title: "No.",
          render: function (data, type, row, meta) {
            return meta.row + 1; // ลำดับ (เริ่มจาก 1)
          },
        },
        { data: "device_name", title: "อุปกรณ์" },
        { data: "ip_address", title: "IP Address" },
        {
          data: "create_at",
          title: "เริ่มใช้งาน",
          render: function (data) {
            const date = new Date(data);
            const options = {
              year: "numeric",
              month: "short",
              day: "numeric",
            };
            return date.toLocaleString("th-TH", options);
          },
        },
        {
          data: null,
          title: "จัดการ",
          className: "text-center",
          render: (data, type, row) => {
            if (!subscription) {
              return `<button class="btn btn-sm btn-danger btnRm" data-device="${row.device_name}" data-endpoint="${row.sub_code}">
                                <span class="material-symbols-outlined">delete</span>
                            </button>`;
            }

            if (subscription.endpoint === row.endpoint) {
              return "<span class='badge bg-success'>อุปกรณ์ปัจจุบัน</span>";
            } else {
              return `<button class="btn btn-sm btn-danger btnRm" data-device="${row.device_name}" data-endpoint="${row.sub_code}">
                                <span class="material-symbols-outlined">delete</span>
                            </button>`;
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
        { width: "50px", targets: 0 },
        { width: "200px", targets: 1 },
        { width: "200px", targets: 2 },
        { width: "200px", targets: 3 },
        { width: "100px", targets: 4 },
      ],
      initComplete: function () {
        $(subTable).find("thead th").css({
          "background-color": "#224788",
          color: "white",
          borderColor: " white",
        });
      },
    });

    // Event Delegation สำหรับปุ่ม Delete
    subTable.addEventListener("click", async (e) => {
      if (e.target.closest(".btnRm")) {
        const btn = e.target.closest(".btnRm");
        const subCode = btn.dataset.endpoint;
        const deviceName = btn.dataset.device;

        const result = await Swal.fire({
          title: "ลบการแจ้งเตือน",
          text: "ต้องการลบรายการแจ้งเตือนของ : " + deviceName + " ? ",
          icon: "question",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "ใช่",
          cancelButtonText: "ไม่",
          allowOutsideClick: false,
        });

        if (result.isConfirmed) {
          await deleteNotif(subCode); // ส่ง endpoint ของ row นั้น
          const notiIcon = '<i class="fa-solid fa-bell-slash"></i>';
          Swal.fire({
            title: "ยกเลิกการแจ้งเตือน",
            text: "ยกเลิกการแจ้งเตือนเรียบร้อยแล้ว",
            icon: "warning",
            iconHtml: notiIcon,
            customClass: {
              icon: "fa-beat",
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
      }
    });
  } catch (error) {
    console.error("Fetch error:", error);
  }
});
