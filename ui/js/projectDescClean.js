let htmlQuillContent = quill.root.innerHTML;
console.log(htmlQuillContent);
function replaceNewlinesWithBr() {
    const displayElement = document.querySelector(".displayF");
    const displayonAlter = document.querySelector("#project_description");
    const richEditorAlter = document.querySelector(".ql-editor");

    console.log(displayonAlter);

    if (displayElement) {
        let content = displayElement.innerHTML;

        // Replace \n, \r, \t, \n\r with <br> tags
        content = content.replace(/(\r\n|\n|\r|\t)/g, "<br/>");
        content = content.replace(/(\t)/g, "&nbsp;&nbsp;&nbsp;&nbsp;");
    } else if (displayonAlter) {
        let content = displayonAlter.innerHTML;
        console.log(content);

        // Replace \n, \r, \t, \n\r with <br> tags
        let modifiedText = content.replace(/\r\n|\\n|\r/g, "<br/>");
        displayonAlter.value = modifiedText;
        richEditorAlter.innerHTML = modifiedText;
    } else {
        console.error('Element with id "displayF" not found');
    }
}

replaceNewlinesWithBr();