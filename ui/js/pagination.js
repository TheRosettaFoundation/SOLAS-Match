let pagePosition = {
    p: 1,
    tt: 0,
    sl: 0,
    tl: 0,
    prev: 0,
    next: 0,
};

let validation = {
    tt: false,
    sl: false,
    tl: false,
};

const projects = document.querySelectorAll("#project");

console.log(projects);
for (let i = 0; i < projects.length; i++) {
    let firstLink = projects[i].children[0];
    let secondLink = projects[i].children[1];
    firstLink.classList.add("text-decoration-none", "link-primary");
    secondLink.classList.add("text-decoration-none", "link-primary");
}

let taskTypeVal = document.getElementById("taskTypes");

let tasksContainer = document.querySelector(".taskPagination");

let recents = document.querySelector(".recents");

let userid;

if (recents) {
    userId = recents.id;

    recents.addEventListener("click", function (e) {
        e.preventDefault();
        fetchRecents();
    });
}

let pages = document.querySelectorAll(".page");

let previous = document.querySelector("#previous");

let next = document.querySelector("#next");

let last = document.querySelector(".last");
let first = document.querySelector(".first");

let countPage = document.querySelector(".last").id;

console.log(countPage);

last.addEventListener("click", function (e) {
    e.preventDefault();

    let prevPage = document.getElementById(pagePosition.p).parentNode;
    console.log(`previous`);

    console.log(previous);

    prevPage.classList.remove("bg-primary", "opacity-75", "text-primary");

    let url = `paged/${countPage}/tt/${pagePosition.tt}/sl/${pagePosition.sl}/tl/${pagePosition.tl}`;

    pagePosition.p = countPage;

    requestPage(url);
});

first.addEventListener("click", function (e) {
    e.preventDefault();

    let prevPage = document.getElementById(pagePosition.p).parentNode;

    prevPage.classList.remove("bg-primary", "opacity-75", "text-primary");

    let firstPage = document.getElementById("1").parentNode;

    pagePosition.p = 1;

    if (pagePosition.p == 1) {
        firstPage.classList.add("bg-primary", "opacity-75", "text-primary");
    }

    let url = `paged/1/tt/${pagePosition.tt}/sl/${pagePosition.sl}/tl/${pagePosition.tl}`;

    pagePosition.p = 1;

    requestPage(url);
});

let selectedLanguage = document.querySelector("#sourceLanguage");
let taskType = document.querySelector("#taskTypes");
let selectL = "";
let selectT = "";
let selectTask = "";

let allPages = document.querySelectorAll(".page");
let listPage = document.querySelectorAll(".listPage");
selectedLanguage.addEventListener("change", function () {
    let page = document.querySelector(".page");
    let url = page.href;
    pagePosition.sl = this.value;
    selectedL = this.value;

    if (selectedL == 0) {
        validation.sl = false;
        console.log(validation);
    } else {
        validation.sl = true;
        console.log(validation);
    }
    console.log(`Value : ${selectedL}`);
    let find = url.indexOf("sl/");
    let findN = url.indexOf("tl");
    let firstL = url.slice(0, find);
    let firstR = url.slice(findN);
    let newUrl = firstL + `sl/${selectedL}/` + firstR;

    allPages.forEach((page) => {
        let firstPart = page.href.split("/tt");
        let endPart = newUrl.split("/tt");
        let finUrl = firstPart[0] + "/tt" + endPart[1];

        page.href = finUrl;
    });
});

taskType.addEventListener("change", function (e) {
    console.log(e.type);

    let page = document.querySelector(".page");
    let url = page.href;

    pagePosition.tt = this.value;

    selectTask = this.value;

    if (selectTask == 0) {
        validation.tt = false;
    } else {
        validation.tt = true;
    }
    console.log(validation);
    let find = url.indexOf("tt/");
    let findN = url.indexOf("/sl");

    let firstL = url.slice(0, find);
    let firstR = url.slice(findN);
    let newUrl = firstL + `tt/${selectTask}` + firstR;
    allPages.forEach((page) => {
        page.href = newUrl;
    });
});

pages.forEach((page) => {
    let hr = page.href;

    let id = page.id;

    page.addEventListener("click", (e) => {
        e.preventDefault();

        for (var i = 0; i < listPage.length; i++) {
            let pageC = listPage[i].firstElementChild;

            if (pageC.id == id) {
                listPage[i].classList.add(
                    "bg-primary",
                    "opacity-75",
                    "text-primary"
                );
            } else {
                listPage[i].classList.remove(
                    "bg-primary",
                    "opacity-75",
                    "text-primary"
                );
            }
        }

        if (page.id == "previous") {
            requestPage(previous.href);

            let newPrevPosition = pagePosition.p > 1 ? pagePosition.p - 1 : 1;

            pagePosition.prev = newPrevPosition;

            let prevP = pagePosition.prev;

            if (pagePosition.p <= 6) {
                let pagePrev = document.getElementById(prevP).parentNode;

                console.log(`pagePrev`);

                console.log(pagePrev);

                pagePrev.classList.add(
                    "bg-primary",
                    "opacity-75",
                    "text-primary"
                );
            }

            let newPrevUrl = `paged/${newPrevPosition}/tt/${pagePosition.tt}/sl/${pagePosition.sl}/tl/${pagePosition.tl}`;

            previous.href = newPrevUrl;

            pagePosition.p = newPrevPosition;
        } else if (page.id == "next") {
            requestPage(next.href);

            let newNextPosition =
                pagePosition.p <= countPage
                    ? pagePosition.p + 1
                    : pagePosition.p;

            let newNextUrl = `paged/${newNextPosition}/tt/${pagePosition.tt}/sl/${pagePosition.sl}/tl/${pagePosition.tl}`;

            pagePosition.next = newNextPosition;

            let nextP = pagePosition.next;

            if (pagePosition.next <= 6) {
                let pageNext = document.getElementById(nextP).parentNode;

                pageNext.classList.add(
                    "bg-primary",
                    "opacity-75",
                    "text-primary"
                );
            }

            if (pagePosition.p < countPage) {
                pagePosition.p = pagePosition.p + 1;
            }

            next.href = newNextUrl;
        } else {
            pagePosition.p = parseInt(page.id);

            let newPrevPosition = pagePosition.p > 1 ? pagePosition.p - 1 : 1;

            let newNextPosition =
                pagePosition.p <= countPage
                    ? pagePosition.p + 1
                    : pagePosition.p;

            let newPrevUrl = `paged/${newPrevPosition}/tt/${pagePosition.tt}/sl/${pagePosition.sl}/tl/${pagePosition.tl}`;

            let newNextUrl = `paged/${newNextPosition}/tt/${pagePosition.tt}/sl/${pagePosition.sl}/tl/${pagePosition.tl}`;

            if (pagePosition.p == 1) {
                previous.classList.add("disabled");
            } else {
                previous.classList.remove("disabled");
            }

            previous.href = newPrevUrl;

            next.href = newNextUrl;

            requestPage(hr);
        }
    });
});

const requestPage = (url) => {
    const req = new XMLHttpRequest();
    req.addEventListener("load", reqListner);
    req.open("GET", url, true);
    req.send();
};

function displayTasks(pages) {
    let parsed;
    let images;
    let projects;

    try {
        parsed = typeof pages == "string" ? JSON.parse(pages) : pages;
    } catch (error) {
        console.log(` invalid json  ${error}`);
    }

    if (parsed.hasOwnProperty("images")) {
        images = parsed.images;
    }

    if (parsed.hasOwnProperty("projects")) {
        projects = parsed.projects;
    }

    let newData = document.createElement("div");

    for (const item of parsed.tasks) {
        let taskType = "";
        let imageId;
        let image;
        let imageHtml;

        const innerDiv = document.createElement("div");
        const itemElement = document.createElement("div");
        itemElement.classList.add(
            "mb-4",
            "bg-body-tertiary",
            "p-3",
            "rounded-3"
        );
        const itemNameElement = document.createElement("div");
        itemNameElement.classList.add("100");

        const itemFlexContainer = document.createElement("id");
        itemFlexContainer.classList.add("d-flex", "justify-content-between");
        const itemSubFlex = document.createElement("div");
        const titleContainer = document.createElement("div");
        const title = document.createElement("div");
        title.classList.add(
            "text-primaryDark",
            "d-inline-block",
            "h3",
            "fw-bold"
        );
        title.textContent = item.title;

        const spanTitle = document.createElement("div");
        const spanImg = document.createElement("img");
        spanImg.src = "/ui/img/question.svg";
        spanImg.classList.add("mx-1");
        spanTitle.appendChild(spanImg);

        title.appendChild(spanImg);

        titleContainer.classList.add(
            "fw-bold",
            "fs-4",
            "d-flex",
            "align-items-center"
        );
        titleContainer.appendChild(title);

        const badgeContainer = document.createElement("div");
        badgeContainer.classList.add("d-flex", "mt-2", "mb-2");

        taskType = type_texts[item.taskType];

        const badge = document.createElement("span");
        badge.classList.add(
            "badge",
            "rounded-pill",

            "border",
            "border-2",
            "border-greenBorder",
            "border-opacity-25",
            "text-white",
            "text-uppercase",
            "fs-7",
            "font-bold"
        );

        badge.style.backgroundColor = colours[item.taskType];
        badge.textContent = taskType;
        badgeContainer.appendChild(badge);

        const badgeW = document.createElement("span");
        badgeW.classList.add(
            "ms-1",
            "badge",
            "rounded-pill",
            "bg-quartenary",
            "border",
            "border-2",
            "border-quartBorder",
            "border-opacity-25",
            "text-white",
            "fs-7",
            "font-bold"
        );

        let typeWord = unit_count_text_shorts[item.taskType];
        badgeW.textContent = `${item.wordCount} ${typeWord}`;

        badgeContainer.appendChild(badgeW);

        if (images) {
            imageId = images[item.id] !== "" ? images[item.id] : "";

            image =
                imageId.length > 2
                    ? `
        <div>

            <div id=""  >
                <img style="width:100px ; height:100px"  src= ${imageId}  class="image" />
            </div>
            </div>

        `
                    : `<div> </div>`;
        }

        let deadline = item.deadline;
        const deadlineDate = new Date(
            deadline.substring(0, 10) + "T" + deadline.substring(11) + "Z"
        );
        let date =
            deadlineDate.getFullYear() +
            "-" +
            (deadlineDate.getMonth() + 1).toString().padStart(2, "0") +
            "-" +
            deadlineDate.getDate().toString().padStart(2, "0");
        const hour = deadlineDate.getHours().toString().padStart(2, "0");
        const min = deadlineDate.getMinutes().toString().padStart(2, "0");
        const sec = deadlineDate.getSeconds().toString().padStart(2, "0");
        let timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

        let languages = `<div class="mt-3 mb-3">
        <span class="mb-1  text-muted">
                        Languages:  ${item.sourceLocale.languageName}  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11 12H21M21 12L18 9M21 12L18 15M7 12C7 12.5304 6.78929 13.0391 6.41421 13.4142C6.03914 13.7893 5.53043 14 5 14C4.46957 14 3.96086 13.7893 3.58579 13.4142C3.21071 13.0391 3 12.5304 3 12C3 11.4696 3.21071 10.9609 3.58579 10.5858C3.96086 10.2107 4.46957 10 5 10C5.53043 10 6.03914 10.2107 6.41421 10.5858C6.78929 10.9609 7 11.4696 7 12Z" stroke="#E8991C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>  ${item.targetLocale.languageName}
                    </span>
        </div>
        <div class="text-muted d-flex me-2" > <div> Due by </div>
        <strong class="d-flex align-items-center"> <div class="mx-2 "> ${date} </div>
         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f89406" class="bi bi-clock" viewBox="0 0 16 16" class="mx-1">
    <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>
    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>
    </svg>  <div class="mx-2"> ${hour}:${min}:${sec} </div>  </strong>
    <div class="ms-2"> ${timezone}</div>

     </div>

        `;

        if (image) {
            imageHtml = document.createRange().createContextualFragment(image);
        }

        const langHtml = document
            .createRange()
            .createContextualFragment(languages);
        let projectItem = projects ? projects[item.id] : "";

        const matches = projectItem.matchAll(/<a.*?>(.*?)<\/a>/g);

        let part1;
        let part2;

        console.log(matches);

        // for (const match of matches) {
        //     const part1 = match[0];
        //     const part2 = match[1];
        //     console.log(anchorTag);
        //     console.log(anchorText);
        // }

        // if (matches) {
        //     console.log(matches);
        // } else {
        //     console.log("No anchor tag found");
        // }

        const viewTask = `<div class ="d-flex justify-content-between align-items-center flex-wrap mt-3">
                            <div> <span class="text-dark"> Part of  ${projectItem} </span> </div>
                            <div class="d-flex justify-content-end mt-2 mt-sm-4 mt-md-0">
                                <a class="btn btn-secondary fs-5 px-3"  href= "task/${item.id}/view" target="_blank">View Task</a>
                            </div>

                            </div>`;

        const viewHtml = document
            .createRange()
            .createContextualFragment(viewTask);
        itemSubFlex.appendChild(titleContainer);
        itemFlexContainer.appendChild(itemSubFlex);
        itemSubFlex.appendChild(badgeContainer);
        itemSubFlex.appendChild(langHtml);

        if (imageHtml) {
            itemFlexContainer.appendChild(imageHtml);
        }
        itemNameElement.appendChild(itemFlexContainer);

        itemElement.appendChild(itemNameElement);
        itemElement.appendChild(viewHtml);

        innerDiv.appendChild(itemElement);

        newData.appendChild(innerDiv);
    }

    newDataString = newData.outerHTML;

    tasksContainer.innerHTML = newDataString;
}

async function fetchRecents() {
    const fetched = await fetch(`/user/${userId}/recent/tasks/`);

    const res = await fetched.json();

    displayTasks(res);

    let page = document.querySelector(".pagination");

    page.remove();
}

function reqListner() {
    let pages = this.response;

    try {
        displayTasks(pages);
    } catch (error) {
        console.log(error);
    }
}
