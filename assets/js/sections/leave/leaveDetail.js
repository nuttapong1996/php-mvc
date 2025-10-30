export function leaveDetail(year, tableSelector, spinerId) {

    const table = document.querySelector(tableSelector);
    const spiner = document.getElementById(spinerId);

    if (spiner) spiner.style.display = 'block';
    if (table) table.style.display = 'none';

    fetch(`api/leave-detail/${year}`)
        .then(res => res.json())
        .then(response => {
            const data = response.data; // ใช้แค่ array ข้างใน
            const dataTable = new DataTable(table, {
                destroy: true,
                data: data,
                columns: [
                    { data: 'no_emp_offlist', title: 'เลขที่ใบลา', className: 'text-nowarp align-middle' },
                    { data: 'type_off_offlist', title: 'ประเภทการลา', className: 'align-middle ' },
                    { data: 'startday_offlist', title: 'วันเริ่มลา', className: 'align-middle' },
                    { data: 'endday_offlist', title: 'วันสิ้นสุดลา', className: 'align-middle' },
                    { data: 'countday_offlist', title: 'จำนวนวันลา', className: 'text-center align-middle' },
                    { data: 'year_leave_offlist', title: 'สิทธิ์ปี', className: 'text-center align-middle' },
                    { data: 'comment_offlist', title: 'หมายเหตุ', className: 'align-middle' }
                ],
                responsive: true,
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
                    { width: '150px', targets: 0 }, 
                    { width: '150px', targets: 1 }, 
                    { width: '100px', targets: 2 },  
                    { width: '100px', targets: 3 },  
                    { width: '80px', targets: 4 },  
                    { width: '80px', targets: 5 },  
                    { width: '200px', targets: 6 },  
                ],
                createdRow: function (row, data) {
                    const type = (data.type_off_offlist || "").trim();
                    if (type.includes("ลาพักผ่อน")) row.classList.add("table-success");
                    if (type.includes("ลากิจ")) row.classList.add("table-warning");
                    if (type.includes("ลาป่วย")) row.classList.add("table-info");
                    if (type.includes("ลาอื่นๆ")) row.classList.add("table-secondary");
                    if (["ขาดงาน", "สาย", "พักงาน"].some(x => type.includes(x))) {
                        row.classList.add("text-danger");
                    }
                },
                initComplete: function () {
                    $(table).find('thead th').css({
                        'background-color': '#224788',
                        'color': 'white',
                        'borderColor': ' white'
                    })
                }
            });
        })
        .catch(error => {
            console.error('โหลดข้อมูลผิดพลาด:', error);
        })
        .finally(() => {
            if (spiner) spiner.style.display = 'none';
            if (table) table.style.display = 'table';
        })

}