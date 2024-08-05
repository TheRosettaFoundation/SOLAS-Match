function replaceNewlinesWithBr() {
    const displayElement = document.querySelector(".displayF");
    const displayonAlter = document.querySelector("#project_description");
    console.log(displayonAlter);

    if (displayElement) {
        let content = displayElement.innerHTML;

        // Replace \n, \r, \t, \n\r with <br> tags
        content = content.replace(/(\\r\\n|\\n|\\r|\\t)/g, "<br/>");
        content = content.replace(/(\\t)/g, "&nbsp;&nbsp;&nbsp;&nbsp;");

        displayElement.innerHTML = content;
    } else if (displayonAlter) {
        let content = displayonAlter.innerHTML;
        console.log(content);

        // Replace \n, \r, \t, \n\r with <br> tags
        content = content.replace(/(\\r\\n|\\n|\\r|\\t)/g, "<br/>");
        content = content.replace(/(\\t)/g, "&nbsp;&nbsp;&nbsp;&nbsp;");
        console.log(content);

        displayonAlter.innerHTML = content;
    } else {
        console.error('Element with id "displayF" not found');
    }
}

replaceNewlinesWithBr();
