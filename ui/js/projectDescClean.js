const url = window.location.href;
const regex = /project\/(\d+)\//;
const project_id = url.match(regex)[1];

const sesskey = document.querySelector('input[name="sesskey"]').value;

console.log(project_id);
console.log(sesskey);

function replaceNewlinesWithBr() {
    const displayElement = document.querySelector(".displayF");

    if (displayElement) {
        let content = displayElement.innerHTML;

        // Replace \n, \r, \t, \n\r with <br> tags
        content = content.replace(/(\\r\\n|\\n|\\r|\\t)/g, "<br/>");
        content = content.replace(/(\\t)/g, "&nbsp;&nbsp;&nbsp;&nbsp;");

        displayElement.innerHTML = content;
    } else {
        console.error('Element with id "displayF" not found');
    }
}

async function cleanDescription(taskIds, matching) {
    let url = `/project/${project_id}/alter`;

    const promises = taskIds.map(async (id) => {
        let reqBody = {
            sesskey,
            task_id: id,
            matching: matching,
        };
        try {
            const response = await fetch(url, {
                method: "POST",
                body: new URLSearchParams(reqBody),
            });

            if (!response.ok) {
                throw new Error("error");
            }

            return response.json();
        } catch (error) {
            console.error(error);
        }
    });

    const results = await Promise.all(promises);
    return results;
}

replaceNewlinesWithBr();
