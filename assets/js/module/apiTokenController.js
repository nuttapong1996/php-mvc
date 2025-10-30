import { getCsrfToken } from "./utils.js";
import { date_inter_format } from "./dateFormat.js";
import Swal from "./sweetalert2.all.min+esm.js";

export async function getApiToken(
  txGroup,
  btnCreate,
  noticeToken,
  tableToken,
  tbodyToken
) {
  const inputGroup = document.getElementById(txGroup);
  const createBtn = document.getElementById(btnCreate);
  const notice = document.getElementById(noticeToken);
  const TokenTable = document.getElementById(tableToken);
  const TbodyToken = document.getElementById(tbodyToken);

  try {
    const response = await fetch("token/gettoken", {
      method: "GET",
      credentials: "include",
      headers: {
        "Content-Type": "application/json",
      },
    });

    if (!response.ok) {
      return;
    }

    const data = await response.json();

    if (data.status === "success") {
      TokenTable.style.display = "table";
      inputGroup.style.display = "none";
      createBtn.style.display = "none";
      notice.style.display = "none";

      const lastUsage =
        data.data.last_usage !== null
          ? date_inter_format.format(new Date(data.data.last_usage))
          : "-";

      const tr = document.createElement("tr");
      tr.innerHTML = `<td>${date_inter_format.format(
        new Date(data.data.created_at)
      )}</td>
                      <td>${lastUsage}</td>
                      <td>${data.data.usage_count ?? 0}</td>
                      <td class="text-center"><button id="btnDelete" class="btn btn-danger p-2"><i class="bi bi-trash"></i></button></td>`;
      const deleteBtn = tr.querySelector("#btnDelete");

      deleteBtn.addEventListener("click", () => {
        deleteApiToken();
      });

      TbodyToken.appendChild(tr);
    } else {
      TokenTable.style.display = "none";
      createBtn.style.display = "block";
      notice.style.display = "block";
    }
  } catch (err) {
    console.error("Fetch error:", err);
  }
}

export async function createApiToken(txGroup, txApi, btnCreate) {
  const CsrfToken = getCsrfToken();
  const inputGroup = document.getElementById(txGroup);
  const apiToken = document.getElementById(txApi);
  const createBtn = document.getElementById(btnCreate);

  try {
    const response = await fetch("token/create", {
      method: "POST",
      credentials: "include",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": CsrfToken,
      },
    });

    if (!response.ok) {
      throw new Error("HTTP error " + response.status);
    }

    const data = await response.json();
    if (data.status === "success") {
      inputGroup.style.display = "flex";
      createBtn.style.display = "none";

      apiToken.value = data.data;
    }
  } catch (error) {
    console.error("Fetch error:", error);
  }
}


export async function deleteApiToken() {
  const CsrfToken = getCsrfToken();

  try {
    const response = await fetch("token/delete", {
      method: "DELETE",
      credentials: "include",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": CsrfToken,
      },
    });
    if (!response.ok) {
      throw new Error("HTTP error" + response.status);
    }
    const data = await response.json();
    if (data.status === "success") {
      Swal.fire({
        title: "ลบ API Token สำเร็จ",
        text: "API Token ถูกลบเรียบร้อยแล้ว",
        icon: "success",
        timer: 2000,
        timerProgressBar: true,
        showConfirmButton: false,
        allowOutsideClick: false,
      }).then((result) => {
        if (result.dismiss === Swal.DismissReason.timer) {
          window.location.reload();
        }
      });
    }
  } catch (error) {
    console.error("Fetch error:", error);
  }
}

export function copyApiToken(input) {
  const apiTx = document.getElementById(input);
  const toast = document.querySelector(".toast");
  toast.classList.add("show");
  setTimeout(() => {
    toast.classList.remove("show");
  }, 3000);
  navigator.clipboard.writeText(apiTx.value);
}
