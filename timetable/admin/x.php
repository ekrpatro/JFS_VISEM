<script>
$(document).ready(function () {
    var dayName = "<?= $day_names[$day_no] ?? '' ?>";
    var sessionName = "<?= $fn_an ?>";
    var deptName = "<?= $dept_name ?>";

    $('#facultyTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv',
            {
                extend: 'excelHtml5',
                title: 'Faculty Free Slots',
                messageTop: 'Department: ' + deptName + '\nDay: ' + dayName + '\nSession: ' + sessionName
            },
            {
                extend: 'pdfHtml5',
                title: 'Faculty Free Slots',
                messageTop: 'Department: ' + deptName + '\nDay: ' + dayName + ' (' + sessionName + ')',
                orientation: 'portrait',
                pageSize: 'A4',
                customize: function (doc) {
                    doc.styles.tableHeader.alignment = 'left';
                    doc.styles.title.fontSize = 14;
                    doc.styles.message.fontSize = 12;
                }
            },
            'print'
        ]
    });
});
</script>
