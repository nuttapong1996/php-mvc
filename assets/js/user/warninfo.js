document.addEventListener('DOMContentLoaded', async () => {
    const warnTable = document.getElementById('warnTable');
    const spinner = document.getElementById('loading');

    spinner.style.display = 'block';
    warnTable.style.display = 'none';


    try {
        const warnRes = await fetch('user/warninfo', {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!warnRes.ok) {
            throw new Error('Error fetch data : ' + warnRes.status);
        }

        const warnData = await warnRes.json();

        if (!warnData.data) {
            warnTable.innerHTML = `
                <tr class='border-0'>
                    <td colspan="5" class="text-center text-danger border-0">ไม่พบข้อมูล</td>
                </tr>
            `;
            return;
        }

        const dataTable = new DataTable(warnTable, {
            destroy: true,
            data: warnData.data,
            columns: [
                {
                    data: 'no_waring_emp',
                    title: 'เลขที่ใบเตือน',
                    className: 'border-danger text-start text-nowrap'
                },
                {
                    data: 'effective_date_waring_emp',
                    title: 'วันที่',
                    className: 'border-danger text-nowrap',
                    render: function (data, type, row) {
                        if (!data) return '';
                        const parts = data.split('-');
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                },
                {
                    data: null,
                    title: 'ระดับการเตือน',
                    className: 'border-danger text-sm-start text-md-center text-lg-center',
                    render: function (data, type, row) {
                        // ตรวจ level ทีละตัว
                        if (row.level_1_waring_emp.trim()) return 1;
                        if (row.level_2_waring_emp.trim()) return 2;
                        if (row.level_3_waring_emp.trim()) return 3;
                        if (row.level_4_waring_emp.trim()) return 4;
                        return '';
                    }
                },
                {
                    data: 'reason_waring_emp',
                    title: 'เหตุผล',
                    className: 'border-danger text-wrap'
                },
                {
                    data: 'comment_waring_emp',
                    title: 'หมายเหตุ',
                    className: 'border-danger text-warp',
                }
            ],
            reponsive: true,
            ordering: false,
            autoWidth: true,
            bLengthChange: false,
            pageLength: 5,
            searching: false,
            pagingType: 'simple',
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.1/i18n/th.json',
            },
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: 'paging',
                bottomEnd: 'info',
            },
            columnDefs: [
                { width: '100px', targets: 0 }, // col เลขที่ใบเตือน
                { width: '100px', targets: 1 }, // col วันที่
                { width: '100px', targets: 2 },  // col ระดับการเตือน
                { width: '200px', targets: 3 },  // col เหตุผล
                { width: '200px', targets: 4 },  // col หมายเหตุ
            ]
        });

    } catch (error) {
        console.error('Error fetching warning information:', error);
    }
    finally {
        spinner.style.display = 'none';
        warnTable.style.display = 'table';
    }
});