function removeRGBString(rgbString) {
    return rgbString.replace("rgb(", "").replace(")", "");
}

function cleanColorDescription() {
    let descriptionField = document.querySelector(".displayF");

    let spansWithStyle = descriptionField.querySelectorAll("span[style]");

    spansWithStyle.forEach(function (span) {
        let test = "rgb(0 ,0 ,0)";
        let testconsole = removeRGBString(test);
        console.log(testconsole);
        if (span.style.color == "black" || span.style.color == "rgb(0, 0, 0)") {
            span.removeAttribute("style");
        }
    });
}

cleanColorDescription();
