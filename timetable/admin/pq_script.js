$(document).ready(function() {
    $('#dateRangeForm').submit(function(event) {
        event.preventDefault();

        let startdate = $('#startdate').val();
        let enddate = $('#enddate').val();

        $.ajax({
            url: 'generate_proqurement_report.php',
            type: 'POST',
            data: { startdate: startdate, enddate: enddate },
            success: function(response) {
                $('#report').html(response);
            },
            error: function() {
                $('#report').html('<p>An error occurred while fetching the report.</p>');
            }
        });
    });
	 $('#prnt_btn').click(function() {
                // Get the report content
				alert("hellor print");
                var reportContent = $('#report').html();

                // Create a new window for the print preview
                var printWindow = window.open('', '', 'height=600,width=800');

                // Write the HTML for the print window
                printWindow.document.write('<html><head><title>Procurement Report</title>');
                printWindow.document.write('</head><body >');
                printWindow.document.write(reportContent);
                printWindow.document.write('</body></html>');

                // Close the document to trigger rendering
                printWindow.document.close();

                // Print the content
                printWindow.print();
            });
	
	
});
