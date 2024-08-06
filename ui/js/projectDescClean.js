function cleanColorDescription() {
    let descriptionField = document.querySelector(".displayF");

    let spansWithStyle = descriptionField.querySelectorAll("span[style]");

    spansWithStyle.forEach(function (span) {
        console.log(span);
    });
}

cleanColorDescription();
