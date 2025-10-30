export async function evalDetail(year) {

    const know = document.getElementById('knowledge');
    const att = document.getElementById('attitude');
    const leave = document.getElementById('leave');

    const response = await fetch(`api/emp-score/${year}`, {
        method: 'GET',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
        }
    });

    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }

    if (response.status === 204) {
        know.innerHTML = '-';
        att.innerHTML = '-';
        leave.innerHTML = '-';
        return;
    }

    const data = await response.json();

    know.innerHTML = data.data.knowledge_assess_emp;
    att.innerHTML = data.data.attitude_behavior_assess_emp;
    leave.innerHTML = data.data.attitude_behavior_assess_emp;
}
