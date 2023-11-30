// Object tracking the page clicled

let pagePosition = {
    p: 0,
    tt: 0,
    sl: 0,
    tl: 0,
};

// Theme
let light = true;
let theme = document.getElementById("theme");

let imgL = document.getElementById("light");
let imgN = document.getElementById("night");
let navi = document.getElementById("nav");
let pages = document.querySelectorAll(".page");
let tasksContainer = document.querySelector(".taskPagination");
let previous = document.querySelector("#previous");
let next = document.querySelector("#next");

let last = document.querySelector(".last");
let first = document.querySelector(".first");
let countPage = document.querySelector(".last").id;

last.addEventListener("click", function (e) {
    e.preventDefault();
    let url = `paged/${countPage}/tt/${pagePosition.tt}/sl/${pagePosition.sl}/tl/${pagePosition.tl}`;
    pagePosition.p = countPage;
    requestPage(url);
});

first.addEventListener("click", function (e) {
    e.preventDefault();
    let url = `paged/1/tt/${pagePosition.tt}/sl/${pagePosition.sl}/tl/${pagePosition.tl}`;
    pagePosition.p = 1;
    requestPage(url);
});

let selectedLanguage = document.querySelector("#sourceLanguage");

let targetLanguage = document.querySelector("#targetLanguage");
let taskType = document.querySelector("#taskTypes");
let selectL = "";
let selectT = "";
let selectTask = "";

let allPages = document.querySelectorAll(".page");

selectedLanguage.addEventListener("change", function () {
    let page = document.querySelector(".page");
    let url = page.href;

    pagePosition.sl = this.value;
    selectedL = this.value;
    console.log(`Value : ${selectedL}`);
    let find = url.indexOf("sl/");
    let findN = url.indexOf("tl");

    let firstL = url.slice(0, find);
    let firstR = url.slice(findN);
    let newUrl = firstL + `sl/${selectedL}/` + firstR;

    allPages.forEach((page) => {
        let firstPart = page.href.split("/tt");
        console.log(firstPart);
        let endPart = newUrl.split("/tt");
        let finUrl = firstPart[0] + "/tt" + endPart[1];
        console.log(finUrl);
        page.href = finUrl;
    });
});

targetLanguage.addEventListener("change", function () {
    let page = document.querySelector(".page");
    let url = page.href;
    targetL = this.value;

    pagePosition.tl = this.value;
    console.log(`Value : ${targetL}`);
    let find = url.indexOf("tl/");
    let firstL = url.slice(0, find);
    let newUrl = firstL + `tl/${targetL}`;
    allPages.forEach((page) => {
        page.href = newUrl;
    });
});

taskType.addEventListener("change", function () {
    let page = document.querySelector(".page");
    let url = page.href;

    pagePosition.tt = this.value;

    selectTask = this.value;
    console.log(`Value : ${selectTask}`);
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

    page.addEventListener("click", (e) => {
        e.preventDefault();

        if (page.id == "previous") {
            requestPage(previous.href);
            let newPrevPosition =
                pagePosition > 1 ? parseInt(pagePosition.p) - 1 : 1;
            let newPrevUrl = `paged/${newPrevPosition}/tt/${pagePosition.tt}/sl/${pagePosition.sl}/tl/${pagePosition.tl}`;
            previous.href = newPrevUrl;
            pagePosition.p = newPrevPosition;
        } else if (page.id == "next") {
            requestPage(next.href);

            console.log(`page position : ${parseInt(pagePosition.p)}`);
            console.log(`countPage : ${countPage}`);
            let newNextPosition =
                parseInt(pagePosition.p) < countPage
                    ? parseInt(pagePosition.p) + 1
                    : parseInt(pagePosition.p);
            let newNextUrl = `paged/${newNextPosition}/tt/${pagePosition.tt}/sl/${pagePosition.sl}/tl/${pagePosition.tl}`;
            next.href = newNextUrl;
            pagePosition.p = newNextPosition;
        } else {
            pagePosition.p = page.id;
            let newPrevPosition =
                parseInt(pagePosition.p) > 1 ? parseInt(pagePosition.p) - 1 : 1;
            let newNextPosition =
                parseInt(pagePosition.p) > countPage
                    ? parseInt(pagePosition.p) + 1
                    : parseInt(pagePosition.p);
            let newPrevUrl = `paged/${newPrevPosition}/tt/${pagePosition.tt}/sl/${pagePosition.sl}/tl/${pagePosition.tl}`;
            let newNextUrl = `paged/${newNextPosition}/tt/${pagePosition.tt}/sl/${pagePosition.sl}/tl/${pagePosition.tl}`;
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

function reqListner() {
    let pages = this.response;
    let newData = document.createElement("div");

    try {
        parsed = JSON.parse(pages);

        let images = parsed.images;

        for (const item of parsed.tasks) {
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
            const itemSubFlex = document.createElement("div");

            itemSubFlex.classList.add("d-flex", "justify-content-between");
            const titleContainer = document.createElement("div");
            const title = document.createElement("div");
            title.classList.add("text-primary", "d-inline-block", "mx-2");
            title.textContent = item.title;
            const spanTitle = document.createElement("span");
            spanTitle.classList.add(
                "fs-5",
                "bg-primary",
                "border-2",
                "border-primary",
                "opacity-75",
                "rounded-circle",
                "d-inline-block",
                "px-2",
                "text-white",
                "align-self-start"
            );
            spanTitle.textContent = "?";
            title.appendChild(spanTitle);

            titleContainer.classList.add("fw-bold", "fs-4", "align-middle");
            titleContainer.appendChild(title);

            const badgeContainer = document.createElement("div");
            badgeContainer.classList.add("d-flex", "mt-2", "mb-2");

            let taskType = "";

            if (item.taskType == 2) {
                taskType = "TRANSLATION";
            } else if (item.taskType == 3) {
                taskType = "REVISION";
            } else {
                taskType = "APPROVAL";
            }

            const badge = document.createElement("button");
            badge.classList.add(
                "rounded-5",
                "bg-greenish",
                "border",
                "border-0"
            );
            const badgeSpan = document.createElement("span");
            badgeSpan.classList.add(
                "fs-6",
                "p-1",
                "text-white",
                "fw-bold",
                "align-middle"
            );
            badgeSpan.textContent = taskType;
            badge.appendChild(badgeSpan);
            badgeContainer.appendChild(badge);

            const badgeW = document.createElement("button");
            badgeW.classList.add(
                "ms-1",
                "rounded-5",
                "bg-quartenary",
                "border",
                "border-0"
            );
            const badgeDiv = document.createElement("div");
            badgeDiv.classList.add(
                "fs-6",
                "p-1",
                "text-white",
                "fw-bold",
                "align-middle"
            );
            badgeDiv.textContent = `${item.wordCount} WORDS`;
            badgeW.appendChild(badgeDiv);
            badgeContainer.appendChild(badgeW);

            let imageId = images[item.id] !== "" ? images[item.id] : "";
            console.log(`image ID ${imageId}`);
            let image =
                imageId.length > 2
                    ? `
                   <div>
                   
                    <div id=""  >
                        <img style="width:100px ; height:100px"  src= ${imageId}  class="image" />
                    </div>
                    </div>
      
                
                `
                    : `<div> </div>`;

            let languages = `<div class="">

                <span class="mb-1  text-muted">
                                Languages:  ${item.sourceLocale.languageName} -  ${item.targetLocale.languageName}
                            </span>
                    
                    
       
                </div>
                   <div class="text-muted " > Due by <strong>${item.deadline} </strong> </div>
                
                `;

            const imageHtml = document
                .createRange()
                .createContextualFragment(image);

            const langHtml = document
                .createRange()
                .createContextualFragment(languages);

            const viewTask = `<div class ="d-flex justify-content-between align-items-center flex-wrap ">
                                    <div> Translation Project for  <span class="text-primary">Translations without Borders </span></div>
                                    <div class="d-flex justify-content-end">
                                        <a class="btn btn-secondary fs-5 px-3"  href="" target="_blank">View Task</a>
                                    </div>
        
                                    </div>`;

            const viewHtml = document
                .createRange()
                .createContextualFragment(viewTask);

            itemSubFlex.appendChild(titleContainer);
            itemSubFlex.appendChild(imageHtml);

            itemFlexContainer.appendChild(itemSubFlex);
            itemFlexContainer.appendChild(badgeContainer);
            itemFlexContainer.appendChild(langHtml);

            itemNameElement.appendChild(itemFlexContainer);

            itemElement.appendChild(itemNameElement);
            itemElement.appendChild(viewHtml);

            innerDiv.appendChild(itemElement);

            newData.appendChild(innerDiv);
        }
        console.log(newData);
        newDataString = newData.outerHTML;

        tasksContainer.innerHTML = newDataString;
    } catch (error) {
        console.log(error);
    }
}

theme.addEventListener("click", function (e) {
    light = !light;
    console.log(light);

    if (light) {
        imgL.classList.remove("d-none");
        imgN.classList.add("d-none");
        document.documentElement.setAttribute("data-bs-theme", "light");
        navi.setAttribute("data-bs-theme", "light");
    } else {
        imgL.classList.add("d-none");
        imgN.classList.remove("d-none");
        document.documentElement.setAttribute("data-bs-theme", "dark");
        navi.setAttribute("data-bs-theme", "dark");
    }
});
