export async function get_current_profile() {
    try {
        const respone = await fetch("user/me", {
            method: "GET",
            credentials: "include",
        });

        if (!respone.ok) {
            throw new Error("HTTP error " + respone.status);
        }

        const data = await respone.json();

        if (data.status === "success") {
            return data.data;
        } else {
            console.warn("กรุณา login ใหม่");
            return null;
        }
    } catch (error) {
        console.error("Fetch error:", err);
        return null;
    }
}

export async function loadEmpData(txtName) {

    const empInfo = document.querySelectorAll('.empInfo');
    const txEmpname = document.getElementById(txtName);


    if (empInfo) {

        try {
            empInfo.forEach(el => {
                el.classList.add('blur-text');
            });

            const profile = await get_current_profile();

            if (txEmpname) { txEmpname.innerHTML = '<b>คุณ' + ' ' + profile.username + '</b>'; }

        } catch (error) {
            console.error('Error fetch data : ' + error);
        } finally {
            empInfo.forEach(el => {
                el.classList.remove('blur-text');
                el.classList.add('loaded');
            });
        }
    }


}
