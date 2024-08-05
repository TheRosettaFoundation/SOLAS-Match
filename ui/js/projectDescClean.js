function replaceNewlinesWithBr() {
    const displayElement = document.getElementById("displayF");

    if (displayElement) {
        let content = displayElement.innerHTML;

        // Replace \n, \r, \t, \n\r with <br> tags
        content = content.replace(/(\r\n|\n|\r|\t)/g, "<br/>");

        displayElement.innerHTML = content;
    } else {
        console.error('Element with id "display" not found');
    }
}

replaceNewlinesWithBr();
