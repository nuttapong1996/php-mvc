export async function leaveSum(year) {
    try {
        const response = await fetch(`api/leave-num/${year}`, {
            method: 'GET',
            credentials: 'include'
        });

        if (!response.ok) {
            throw new Error('HTTP error ' + response.status);
        }

        const data = await response.json();

        if (data.status === 'success') {
            return data.data;
        }else{
            console.warn('ไม่พบข้อมูล');
            return null;
        }
    } catch (error) {
        console.error('Fetch error:', error);
        return null;
    }
}