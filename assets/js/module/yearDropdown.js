export function yearDropdown(selectID , prvYear) {

    const leaveYear = document.getElementById(selectID);
    if (leaveYear) {
        for (let i = 0; i <= prvYear; i++) {
            const opt = document.createElement('option');
            opt.value = new Date().getFullYear() - i;
            opt.textContent = new Date().getFullYear() - i;
            leaveYear.appendChild(opt);
        }
    }
}