import { yearDropdown } from '../../module/yearDropdown.js';
import { evalDetail } from './evalDetail.js';

const yearSelect = document.getElementById('evalyear');

document.addEventListener('DOMContentLoaded', () => {
    yearDropdown('evalyear', 2);

    const txYear = document.getElementById('txYear');
    const evalyear = document.getElementById('evalyear');

    let year = new Date().getFullYear();
    txYear.innerHTML = year;

    evalDetail(year);
});

yearSelect.addEventListener('change' , async()=>{
    let year = yearSelect.value;
    txYear.innerHTML = year;
    evalDetail(year);
});
