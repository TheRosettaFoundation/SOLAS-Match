let uniqueElements = [];
let taskSelected = {};
let tobefetched = {};
let countFetch = [];

const url = window.location.href;
const regex = /project\/(\d+)\//;
const project_id = url.match(regex)[1];
let errorTag = document.querySelector(".modal-error");
console.log(errorTag);

let taskToRestrict = [];

const sesskey = document.querySelector('input[name="sesskey"]').value;

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
    errorTag.classList.add("d-none");

    if (form_1) {
        let children = form_1.querySelectorAll(".extended");

        children.forEach((elt) => {
            form_1.removeChild(elt);
        });
    }
});

async function updateTaskRestrictions(taskIds, matching) {
    let url = `/project/${project_id}/view`;

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

async function getUsersCount(taskIds) {
    let url = `/project/${project_id}/view`;

    const promises = taskIds.map(async (id) => {
        let reqBody = {
            sesskey,
            translators_count: id,
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

            // console.log(typeIndex);
            let typeIndex = task_types.indexOf(parseInt(taskType));

            const taskName = type_texts[typeIndex];
            let value = checkbox.value;

            let selectedCol = document.getElementById(value);
            if (selectedCol) {
                let statusTag = selectedCol.querySelector("a:first-child");
                if (statusTag) {
                    let statusValidText = statusTag.textContent.trim();
                    if (
                        statusValidText == "Complete" ||
                        statusValidText == "In Progress"
                    ) {
                        return;
                    }
                }
            }

            if (taskName) {
                if (!taskSelected[taskName]) {
                    taskSelected[taskName] = [value];
                } else {
                    taskSelected[taskName].push(value);
                }
            }

            uniqueElements = Object.keys(taskSelected);
        });

        if (uniqueElements.length > 0) {
            for (const key in taskSelected) {
                let selected = taskSelected[key];
                let count = selected[0];

                if (!tobefetched[key]) {
                    tobefetched[key] = count;
                }
            }

            countFetch = Object.values(tobefetched);
        }

        let call = await getUsersCount(countFetch);

        let nativeMatching = {};

        call.forEach((elt) => {
            if (elt.length > 0) {
                let taskId = elt[0].task_id;
                nativeMatching[taskId] = {};
                nativeMatching[taskId].native_matching_0 =
                    elt[0].native_matching_0;
                nativeMatching[taskId].native_matching_1 =
                    elt[0].native_matching_1;
                nativeMatching[taskId].native_matching_2 =
                    elt[0].native_matching_2;
                nativeMatching[taskId].native_matching_active_0 =
                    elt[0].native_matching_active_0;
                nativeMatching[taskId].native_matching_active_1 =
                    elt[0].native_matching_active_1;
                nativeMatching[taskId].native_matching_active_2 =
                    elt[0].native_matching_active_2;
            } else {
                nativeMatching = 0;
            }
        });

        uniqueElements.forEach((elt) => {
            let taskId = tobefetched[elt];
            let extendedEL = nativeMatching[taskId]
                ? `<div class="d-flex mt-4 mb-2 align-items-center justify-content-between extended">
                <div class="me-4 elt text-break textwrap"></div>

                <select class="form-select ms-2 w-75 selectedId" aria-label="Default select example">
                <option selected value="no"> Select Restrictions</option>
                    <option value="0"> </br>No restriction<span class="nocm">, Matching CMs : ${nativeMatching[taskId].native_matching_0}</span> </br>
                <span class="nosm">, Successful CMs : ${nativeMatching[taskId].native_matching_active_0} </span> </option>
                    <option value="1">Matching Native Language<span class="mlCM">, Matching CMs : ${nativeMatching[taskId].native_matching_1}</span>
                <span class="slCM">, Successful CMs : ${nativeMatching[taskId].native_matching_active_1}</span></option>
                    <option value="2">Matching Native Language and Locale/Country<span class="mCM">, Matching CMs : ${nativeMatching[taskId].native_matching_2}</span>
                <span class="sCM">, Successful CMs : ${nativeMatching[taskId].native_matching_active_2}</span></option>
                </select>

                </div>`
                : `<div class="d-flex mt-4 mb-2 align-items-center justify-content-between extended">
                <div class="me-4 elt text-break textwrap"></div>

                <select class="form-select ms-2 w-75 selectedId" aria-label="Default select example" disabled">
                <option selected value="no" disabled> Select Restrictions</option>
                    <option value="0" disabled> </br>No restriction<span class="nocm">,Matching CMs : 0</span> </br>
                <span class="nosm">,Successful CMs : 0 </span> </option>
                    <option value="1" disabled>Matching Native Language<span class="mlCM">,Matching CMs : 0</span>
                <span class="slCM">,Successful CMs : 0</span></option>
                    <option value="2" disabled>Matching Native Language and Locale/Country<span class="mCM">,Matching CMs : 0 </span>
                <span class="sCM">,Successful CMs : 0</span></option>
                </select>

                </div>`;
            if (!nativeMatching[taskId]) {
                errorTag.classList.remove("d-none");
                errorTag.textContent = "There are no available task !";
            }
            let extendedHtml = document
                .createRange()
                .createContextualFragment(extendedEL);

            let taskelt = extendedHtml.querySelector(".elt");
            let selectNative = extendedHtml.querySelector(".selectedId");
            selectNative.addEventListener("change", async (e) => {
                const matching = e.target.value; // Get the first selected option

                if (matching == "no") {
                    taskToRestrict = [];
                    return taskToRestrict;
                }

                let tasksToupdate = taskSelected[elt];
                taskToRestrict = [...tasksToupdate];

                let updated = await updateTaskRestrictions(
                    tasksToupdate,
                    matching
                );

                updated.forEach((elt) => {
                    let updatedTask = document.getElementById(elt);
                    console.log(updatedTask);
                    let status = updatedTask
                        ? updatedTask.querySelector("span:first-child")
                        : null;
                    if (!status) {
                        errorTag.classList.remove("d-none");
                        errorTag.textContent = "error";
                        return;
                    }
                    let newNative = updatedTask.querySelector("div");

                    let statusText = status.textContent.trim();
                    if (statusText == "Unclaimed" || statusText == "Waiting") {
                        switch (matching) {
                            case "0":
                                newNative.innerHTML = "";
                                break;

                            case "1":
                                newNative.innerHTML = `
                                    <span
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        data-bs-custom-class="custom-tooltip"
                                        data-bs-title="Matching Native Language"
                                    >
                                        <img
                                            src="/ui/img/Native lm.svg"
                                            alt="Matching Native language icon"
                                            width="20%"
                                            height="20%"
                                        />
                                    </span>`;
                                break;

                            case "2":
                                newNative.innerHTML = `
                                    <span
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        data-bs-custom-class="custom-tooltip"
                                        data-bs-title="Matching Native Language and Variant"
                                    >
                                        <img
                                            src="/ui/img/Native lcm.svg"
                                            alt="Matching Native Language and Variant icon"
                                            width="20%"
                                            height="20%"
                                        />
                                    </span>`;
                                break;
                        }
                    }
                });
            });

            taskelt.textContent = elt;

            form_1.appendChild(extendedHtml);
        });
    });
});
