
document.addEventListener('DOMContentLoaded', function () {
    const leaveYear = document.getElementById('leaveYear');
    let prvYear = 2;

    if (leaveYear) {
        for (let i = 0; i <= prvYear; i++) {
            const opt = document.createElement('option');
            opt.value = new Date().getFullYear() - i;
            opt.textContent = new Date().getFullYear() - i;
            leaveYear.appendChild(opt);
        }
    }
});