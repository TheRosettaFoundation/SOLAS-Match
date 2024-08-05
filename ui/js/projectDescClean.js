function replaceNewlinesWithBr() {
    const displayElement = document.querySelector(".displayF");

    if (displayElement) {
        let content = displayElement.innerHTML;

        // Replace \n, \r, \t, \n\r ,&nbsp  with <br> tags
        content = content.replace(/(\r\n|\n|\r|\t|&nbsp)/g, "<br/>");

        displayElement.innerHTML = content;
    } else {
        console.error('Element with id "displayF" not found');
    }
}

replaceNewlinesWithBr();
