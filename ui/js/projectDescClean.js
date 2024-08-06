function cleanColorDescription() {
    let descriptionField = document.querySelector(".displayF");

    let spansWithStyle = descriptionField.querySelectorAll("span[style]");

    spansWithStyle.forEach(function (span) {
        if (span.style.color == "black") {
            console.log("inside");
            span.removeAttribute("style");
        }
    });
}

cleanColorDescription();
