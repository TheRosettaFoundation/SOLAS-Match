function cleanColorDescription() {
    let descriptionField = document.querySelector(".displayF");

    let spansWithStyle = descriptionField.querySelectorAll("span[style]");

    spansWithStyle.forEach(function (span) {
        if (span.style.color == "black" || span.style.color == "rgb(0, 0, 0)") {
            span.removeAttribute("style");
        }
    });
}

cleanColorDescription();
