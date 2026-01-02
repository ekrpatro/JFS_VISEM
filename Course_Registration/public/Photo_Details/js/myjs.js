      
function showDetails() {
    let name = document.getElementById("nameSelect").value;

    let photo = document.getElementById("photo");
    let info = document.getElementById("infoText");

    // Remove frame before adding new one
    photo.classList.remove("photo-james");
	//photo-james-blue
	 photo.classList.remove("photo-james-blue");

    // Mapping names to photos
    let photos = {
        "guido": "images/guido.jpg",
        "dennis": "images/dennis.jpg",
        "james": "images/james.jpg",
        "stroustrup": "images/stroustrup.jpg",
        "juergen": "images/juergen.jpg",
        "krishna": "images/krishna.jpg",

        "bill_gates": "images/bill_gates.jpg",
        "steve_jobs": "images/steve_jobs.jpg",
        "elon_musk": "images/elon_musk.jpg",
        "zukerberg": "images/zukerberg.jpg",
        "linus_torvalds": "images/linus_torvalds.jpeg",
        "tim_berner": "images/tim_berner.jpg",
        "Sundar_Pichai": "images/Sundar_Pichai.jpg",
        "satyanadella": "images/satyanadella.jpg",                
        "AdaLovelace": "images/AdaLovelace.jpg",
        "madhu": "images/madhu.jpg"
    };

    // Mapping details
    let details = {
        "guido": "Guido van Rossum created Python in 1991.",
        "dennis": "Dennis Ritchie developed C and co-created UNIX.",
        "james": "James Gosling invented Java in 1995.",
        "stroustrup": "Bjarne Stroustrup created C++.",
        "juergen": "Juergen Hoeller is the co-creator of Spring Framework.",
        "krishna": "E Krishna Rao Patro — JFS Trainer.",
        "madhu": "B. Madhusudhan Rao — JFS Trainer.",
        "bill_gates": "Bill Gates co-founded Microsoft.",
        "steve_jobs": "Steve Jobs co-founded Apple.",
        "elon_musk": "Elon Musk leads Tesla & SpaceX.",
        "zukerberg": "Mark Zuckerberg founded Facebook.",
        "linus_torvalds": "Linus Torvalds created Linux.",
        "tim_berner": "Tim Berners-Lee invented the World Wide Web.",
        "Sundar_Pichai": "Sundar Pichai is the CEO of Google.",
        "satyanadella": "Satya Nadella is the CEO of Microsoft.",
        "AdaLovelace": "Ada Lovelace was the first computer programmer."
    };

    if (name === "") {
        photo.style.display = "none";
        info.innerHTML = "Please select a person from the dropdown.";
        return;
    }

    photo.src = photos[name];
    photo.style.display = "block";
    info.innerHTML = details[name];

    // ⭐ Apply special frame ONLY for Elon Musk
    if (name === "james") {
        photo.classList.add("photo-james");
		photo.classList.add("photo-james-blue");
    }
}

        