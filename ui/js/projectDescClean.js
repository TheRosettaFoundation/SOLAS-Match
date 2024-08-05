// Function to replace newline characters with <br> tags
function replaceNewlinesWithBr() {
    // Select the element with id 'display'
    const displayElement = document.getElementById("displayF");

    // Check if the element exists
    if (displayElement) {
        // Get the current HTML content of the element
        let content = displayElement.innerHTML;

        // Replace \n, \r, \t, \n\r with <br> tags
        content = content.replace(/(\r\n|\n|\r|\t)/g, "<br/>");

        // Update the element's HTML with the modified content
        displayElement.innerHTML = content;
    } else {
        console.error('Element with id "display" not found');
    }
}

replaceNewlinesWithBr();
