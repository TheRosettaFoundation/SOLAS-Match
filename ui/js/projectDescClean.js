function cleanColorDescription() {
    let descriptionField = document.querySelector(".displayF");

    let spansWithStyle = descriptionField.querySelectorAll("span[style]");

    let test = "rgb(0 ,0 ,0)";
    let testconsole = test.substring(1, test.length - 1);
    console.log(testconsole);

    spansWithStyle.forEach(function (span) {
        if (span.style.color == "black" || span.style.color == "rgb(0, 0, 0)") {
            span.removeAttribute("style");
        }
    });
}

cleanColorDescription();
