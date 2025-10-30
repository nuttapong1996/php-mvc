import {
  getApiToken,
  createApiToken,
  copyApiToken,
} from "./module/apiTokenController.js";

const createBtn = document.getElementById("btnCreate");
const copyBtn = document.getElementById("btnCopy");

document.addEventListener("DOMContentLoaded", async () => {
  getApiToken("divToken", "btnCreate", "noticeToken" ,"tokenTable" ,"tokenTbody");
});

createBtn.addEventListener("click", () => {
  createApiToken("divToken", "txToken" , "btnCreate");
});

copyBtn.addEventListener("click", () => {
  copyApiToken("txToken");
});
