import { highlightCurrentPage } from './../../module/nav.js';
import { leaveSum } from './leaveSum.js';

// call navigate button function for Sidebar 
highlightCurrentPage();

const yearSelect = document.getElementById('leaveYear');
const curYear = document.getElementById('curYear');

const vecationLeave = document.getElementById('vec_leave');
const businessLeave = document.getElementById('business_leave');
const sickLeave = document.getElementById('sick_leave');
const otherLeave = document.getElementById('other_leave');
const absence = document.getElementById('absence');
const late = document.getElementById('late');
const suspend = document.getElementById('suspend');

const vecationRemain = document.getElementById('vec_leave_remain');
const businessRemain = document.getElementById('business_leave_remain');
const sickRemain = document.getElementById('sick_leave_remain');


let leaveYear = new Date().getFullYear();
curYear.innerHTML = leaveYear;

document.addEventListener('DOMContentLoaded', async function () {
    await assignLeaveValue();
});

yearSelect.addEventListener('change', async function () {

    if (yearSelect.value !== '') {
        leaveYear = yearSelect.value;
        curYear.innerHTML = leaveYear;
    }

    vecationLeave.innerHTML = '-';
    businessLeave.innerHTML = '-';
    sickLeave.innerHTML = '-';
    otherLeave.innerHTML = '-';
    absence.innerHTML = '-';
    late.innerHTML = '-';
    suspend.innerHTML = '-';
    vecationRemain.innerHTML = '-';
    businessRemain.innerHTML = '-';
    sickRemain.innerHTML = '-';

    await assignLeaveValue(leaveYear);
});

async function assignLeaveValue() {

    const leaveNum = await leaveSum(leaveYear);

    if (leaveSum) {
        vecationLeave.innerHTML = leaveNum.vacation ?? 0;
        businessLeave.innerHTML = leaveNum.business ?? 0;
        sickLeave.innerHTML = leaveNum.sick ?? 0;
        otherLeave.innerHTML = leaveNum.other ?? 0;
        absence.innerHTML = leaveNum.absent ?? 0;
        late.innerHTML = leaveNum.late ?? 0;
        suspend.innerHTML = leaveNum.suspend ?? 0;
        vecationRemain.innerHTML = leaveNum.remain_vacation ?? 0;
        businessRemain.innerHTML = leaveNum.remain_business ?? 0;
        sickRemain.innerHTML = leaveNum.remain_sick ?? 0;
    } else {
        vecationLeave.innerHTML = 0;
        businessLeave.innerHTML = 0;
        sickLeave.innerHTML = 0;
        otherLeave.innerHTML = 0;
        absence.innerHTML = 0;
        late.innerHTML = 0;
        suspend.innerHTML = 0;
        vecationRemain.innerHTML = 0;
        businessRemain.innerHTML = 0;
        sickRemain.innerHTML = 0;
    }
}


