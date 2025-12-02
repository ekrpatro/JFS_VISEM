// Auto-calculate total fee
let subjects = document.querySelectorAll(".subject");
let totalBox = document.getElementById("total");

subjects.forEach(item => {
    item.addEventListener("change", () => {
        let total = 0;

        subjects.forEach(sub => {
            if (sub.checked) {
                total += parseInt(sub.value);
            }
        });

        totalBox.innerText = "₹" + total;
    });
});

// Optional: form submit
document.getElementById("regForm").addEventListener("submit", function(e){
    e.preventDefault();

    let selectedSubjects = [];
    let totalFee = 0;

    subjects.forEach(sub => {
        if (sub.checked) {

            // Get subject name from the label text
            let subjectName = sub.parentElement.innerText.trim();
            selectedSubjects.push(subjectName);

            totalFee += parseInt(sub.value);
        }
    });

    let resultDiv = document.getElementById("result");

    if (selectedSubjects.length === 0) {
        resultDiv.style.display = "block";
        resultDiv.innerHTML = '<p class="error">Please select at least one subject.</p>';
        return;
    }

    let studentName = document.getElementById("name").value;

    // Validate student name
    if (!studentName || studentName.trim() === "") {
        resultDiv.style.display = "block";
        resultDiv.innerHTML = '<p class="error">Please enter the student name.</p>';
        return;
    }

    // Build HTML for result
    let html = '<h3>Registration Details</h3>';
    html += '<p><strong>Student Name:</strong> ' + studentName + '</p>';
    html += '<p><strong>Selected Subjects:</strong></p><ul>';
    selectedSubjects.forEach(sub => {
        html += '<li>' + sub + '</li>';
    });
    html += '</ul>';
    html += '<p><strong>Total Fee:</strong> ₹' + totalFee + '</p>';

    // Show in the result div
    resultDiv.style.display = "block";
    resultDiv.innerHTML = html;
    resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
});
