// Auto-calculate total fee
let subjects = document.querySelectorAll(".subject");
let totalBox = document.getElementById("total");
let res = document.getElementById("result");

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
      e.preventDefault();

    let selectedSubjects = [];
    let totalFee = 0;
    let cnt=0;
    subjects.forEach(sub => {
        if (sub.checked) {
			cnt++;
            // Get subject name from the label text
            let subjectName = sub.parentElement.innerText.trim();
            selectedSubjects.push(cnt+"."+subjectName);

            totalFee += parseInt(sub.value);
        }
    });

    if (selectedSubjects.length === 0) {
        alert("Please select at least one subject.");
        return;
    }

    let studentName = document.getElementById("name").value;

    let message =
        "Student Name: " + studentName + "<br>" +
        "Selected Subjects:<br> " + selectedSubjects.join("<br>") + "\n\n" +
        "Total Fee: ₹" + totalFee;
		
		res.innerHTML=message;
	res.style.display = "block";
    alert(message);
});
