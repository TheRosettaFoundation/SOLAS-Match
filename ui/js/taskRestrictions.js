let uniqueElements = [];
let taskSelected = {};
let tobefetched = {};
let countFetch = [];

const sesskey = document.querySelector('input[name="sesskey"]').value;

console.log(sesskey);

const myModalEl = document.getElementById("exampleModalToggle");

let form_1 = myModalEl.querySelector("form");

let restrictionsB = document.querySelectorAll(".restrictions");

let error = `<div class="text-danger fw-bold extended"> Please select a task </div>`;

let errorHtml = document.createRange().createContextualFragment(error);

myModalEl.addEventListener("shown.bs.modal", (event) => {
    if (Object.keys(taskSelected).length === 0) {
        form_1.appendChild(
            document.createRange().createContextualFragment(error)
        );
    }
});

myModalEl.addEventListener("hide.bs.modal", (event) => {
    // Clear state
    taskSelected = {};
    uniqueElements = [];
    tobefetched = {};
    countFetch = [];

    if (form_1) {
        let children = form_1.querySelectorAll(".extended");

        console.log(children);

        children.forEach((elt) => {
            form_1.removeChild(elt);
        });
    }
});

async function getUsersCount(task_id) {
    let url = `/project/${task_id}/view`;

    const taskIds = { sesskey, translators_count: task_id, project_id: 9884 };

    try {
        const response = await fetch(url, {
            method: "POST",
            body: new URLSearchParams(taskIds),
        });

        if (!response.ok) {
            throw new Error("error");
        }
        console.log(error);
        return response;
    } catch (error) {
        console.error(error);
    }
}

restrictionsB.forEach((elt) => {
    elt.addEventListener("click", async (e) => {
        const clickedElement = e.target;
        const parent =
            clickedElement.parentElement.parentElement.nextElementSibling;

        const checkedCheckboxes = parent.querySelectorAll(
            'input[type="checkbox"]:checked'
        );

        checkedCheckboxes.forEach((checkbox) => {
            const taskType = checkbox.getAttribute("data-task-type");
            const taskName = type_texts[taskType];
            let value = checkbox.value;

            if (taskName) {
                if (!taskSelected[taskName]) {
                    taskSelected[taskName] = [value];
                } else {
                    taskSelected[taskName].push(value);
                }
            }

            console.log("######################");
            console.log(taskSelected);
            uniqueElements = Object.keys(taskSelected);
            console.log(uniqueElements);
            console.log("######################");
        });

        if (uniqueElements.length > 0) {
            for (const key in taskSelected) {
                console.log(key);
                console.log(taskSelected[key]);

                let selected = taskSelected[key];
                let count = selected[0];

                if (!tobefetched[key]) {
                    tobefetched[key] = count;
                }
            }

            countFetch = Object.values(tobefetched);
            console.log("countFetch");
            console.log(countFetch);
        }

        let call = await getUsersCount(countFetch[0]);

        console.log(call);

        uniqueElements.forEach((elt) => {
            let extendedEL = `<div class="d-flex mt-4 mb-2 align-items-center justify-content-between extended">
                <div class="me-4 elt"></div>
               
                <select class="form-select ms-2 w-75" aria-label="Default select example">
                <option selected> Select Restrictions</option> 
                    <option value="0"> </br>No restriction <span>, Matching CMs : #</span> </br>
                <span>, Successful CMs : #</span> </option>
                    <option value="2">Matching Native Language <span> , Matching CMs : #</span>
                <span>, Successful CMs : #</span></option>
                    <option value="3">Matching Native Language and Locale/Country <span>, Matching CMs : #</span>
                <span>, Successful CMs : #</span></option>
                </select>

                </div>`;

            let extendedHtml = document
                .createRange()
                .createContextualFragment(extendedEL);
            let taskelt = extendedHtml.querySelector(".elt");
            taskelt.textContent = elt;
            let taskId = tobefetched[elt];
            taskelt.setAttribute("id", taskId);
            form_1.appendChild(extendedHtml);
        });
    });
});
