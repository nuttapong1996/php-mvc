import { highlightCurrentPage } from './../../module/nav.js';
import { leaveDetail } from './leaveDetail.js';
// call navigate button function
highlightCurrentPage();

const yearSelect = document.getElementById('leaveYear');
const curYear = document.getElementById('curYear');

let leaveYear = new Date().getFullYear();
curYear.innerHTML = leaveYear;

document.addEventListener('DOMContentLoaded', async function () {
    await leaveDetail(leaveYear,'#report' ,'loading');
});

yearSelect.addEventListener('change', async function () {
    if (yearSelect.value !== '') {
        leaveYear = yearSelect.value;
        curYear.innerHTML = leaveYear;
    }
    await leaveDetail(leaveYear ,'#report' ,'loading');
});
