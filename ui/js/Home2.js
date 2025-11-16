var parameters; // Instance of Parameters Class holding data retrieved from Server (e.g. Translations)
var parametersLoaded = false;

// Passed from PHP
var siteLocation;

$(document).ready(documentReady);

function getSetting(text) {
    return document.getElementById(text).innerHTML;
}

/**
 * Called by the DOM when the Document is Ready.
 */
function documentReady() {
    siteLocation = getSetting("siteLocation");
    parameters = new Parameters(loadingComplete);
}

function loadingComplete() {
    parametersLoaded = true;

    renderTaskDetails();
}

/**
 * Called by initial load for first page and also when a new page has been "rendered".
 */
function renderTaskDetails() {
    if (!parametersLoaded) return;

    $(".process_created_time_utc").each(function () {
        $(this).removeClass("process_created_time_utc");
        var utcTime = $(this).text();
        var seconds = Math.floor(
            new Date().getTime() / 1000 - parseInt(utcTime)
        );
        var minutes = Math.floor(seconds / 60);
        var hours = Math.floor(minutes / 60);
        var days = Math.floor(hours / 24);
        var text;
        if (days > 0) {
            text = parameters
                .getTranslation("common_added_days")
                .replace("%s", days);
        } else if (hours > 0) {
            text = parameters
                .getTranslation("common_added_hours")
                .replace("%s", hours);
        } else if (minutes > 0) {
            text = parameters
                .getTranslation("common_added_minutes")
                .replace("%s", minutes);
        } else {
            text = parameters
                .getTranslation("common_added_seconds")
                .replace("%s", seconds);
        }

        $(this).html(text);
        $(this).css("visibility", "visible");
    });

    $(".process_deadline_utc").each(function () {
        $(this).removeClass("process_deadline_utc");
        var utcTime = $(this).text();

        utcTime = parseInt(utcTime) * 1000;
        var deadline = new Date(utcTime);
        var m = deadline.getMonth() + 1;
        if (m < 10) {
            m = "0" + m;
        }
        var d = deadline.getDate();
        if (d < 10) {
            d = "0" + d;
        }
        var h = deadline.getHours();
        if (h < 10) {
            h = "0" + h;
        }
        var mi = deadline.getMinutes();
        if (mi < 10) {
            mi = "0" + mi;
        }
        $(this).html(
            parameters.getTranslation("common_due_by").replace(
                "%s",

                "<div class='d-flex align-items-center  ms-2 me-4 '>" +
                    `<div class="me-2">` +
                    deadline.getFullYear() +
                    "-" +
                    m +
                    "-" +
                    d +
                    `</div>` +
                    `<div class=" d-flex align-items-center wrap mx-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f89406" class="bi bi-clock" viewBox="0 0 16 16" class="ms-2 me-1">
                        <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>
                        </svg>


                  ` +
                    " " +
                    `<span class="mx-2"> ` +
                    h +
                    ":" +
                    mi +
                    ":00 " +
                    " " +
                    "</span>" +
                    "</div>" +
                    "</div>"
            ) +
                `<div class="fw-bold">` +
                Intl.DateTimeFormat().resolvedOptions().timeZone +
                `</div>`
        );

        $(this).css("visibility", "visible");
    });

    $(".process_deadline_utc_if_possible").each(function () {
        $(this).removeClass("process_deadline_utc_if_possible");
        var utcTime = $(this).text();

        utcTime = parseInt(utcTime) * 1000;
        var deadline = new Date(utcTime);
        var m = deadline.getMonth() + 1;
        if (m < 10) {
            m = "0" + m;
        }
        var d = deadline.getDate();
        if (d < 10) {
            d = "0" + d;
        }
        var h = deadline.getHours();
        if (h < 10) {
            h = "0" + h;
        }
        var mi = deadline.getMinutes();
        if (mi < 10) {
            mi = "0" + mi;
        }
        $(this).html(
            parameters.getTranslation("common_due_by").replace(
                "%s",

                "<div class='d-flex align-items-center  ms-2 me-4 '>" +
                    `<div class="me-2">` +
                    deadline.getFullYear() +
                    "-" +
                    m +
                    "-" +
                    d +
                    `</div>` +
                    `<div class=" d-flex align-items-center wrap mx-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f89406" class="bi bi-clock" viewBox="0 0 16 16" class="ms-2 me-1">
                        <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>
                        </svg>


                  ` +
                    " " +
                    `<span class="mx-2"> ` +
                    h +
                    ":" +
                    mi +
                    ":00 " +
                    "</span>" +
                    "</div>" +
                    "</div>"
            ) +
                `<div class="fw-bold">` +
                Intl.DateTimeFormat().resolvedOptions().timeZone +
                ` or earlier, if possible</div>`
        );

        $(this).css("visibility", "visible");
    });

    $(".process_deadline_utc_new_home_if_possible").each(function () {
        $(this).removeClass("process_deadline_utc_new_home_if_possible");
        var utcTime = $(this).text();

        utcTime = parseInt(utcTime) * 1000;
        var deadline = new Date(utcTime);
        var m = deadline.getMonth() + 1;
        if (m < 10) {
            m = "0" + m;
        }
        var d = deadline.getDate();
        if (d < 10) {
            d = "0" + d;
        }
        var h = deadline.getHours();
        if (h < 10) {
            h = "0" + h;
        }
        var mi = deadline.getMinutes();
        if (mi < 10) {
            mi = "0" + mi;
        }
        $(this).html(
            parameters.getTranslation("common_due_by").replace(
                "%s",
                    "" +
                    deadline.getFullYear() +
                    "-" +
                    m +
                    "-" +
                    d +
                    `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f89406" class="bi bi-clock" viewBox="0 0 16 16">
                        <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>
                    </svg>` +
                    " " +
                    h +
                    ":" +
                    mi +
                    ":00 "
            ) +
            Intl.DateTimeFormat().resolvedOptions().timeZone + " or earlier, if possible"
        );

        $(this).css("visibility", "visible");
    });

    $(".process_deadline_utc_non_timezone").each(function () {
        $(this).removeClass("process_deadline_utc_non_timezone");
        const max_translation_deadline = $(this).text();
        let   max_translation_deadline_text = "";
        const pos_colon = max_translation_deadline.indexOf(":");
        if (max_translation_deadline.indexOf("Completed") > 0) {
            max_translation_deadline_text = max_translation_deadline.substring(0, pos_colon + 2) + '<div class="fw-bold d-flex align-items-center ms-2 me-4"><div class="me-2"> Completed </div></div>';
        } else {
            const deadline = max_translation_deadline.substring(pos_colon + 2);
            const deadlineDate = new Date(
                `${deadline.substring(0, 10)}T${deadline.substring(11)}Z`
            );
            const date = `${deadlineDate.getFullYear()}-${(deadlineDate.getMonth() + 1)
                .toString().padStart(2, "0")}-${deadlineDate.getDate()
                .toString().padStart(2, "0")}`;
            const hour = deadlineDate.getHours().toString().padStart(2, "0");
            const min = deadlineDate.getMinutes().toString().padStart(2, "0");
            const sec = deadlineDate.getSeconds().toString().padStart(2, "0");

            max_translation_deadline_text =
                max_translation_deadline.substring(0, pos_colon + 2) +
                '<div class="fw-bold d-flex align-items-center ms-2 me-4">' +
                    '<div class="me-2"> ' + date + ' </div>' +
                    '<div class="d-flex align-items-center wrap mx-2">' +
                        '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f89406" class="bi bi-clock" viewBox="0 0 16 16" class="ms-2 me-1">' +
                            '<path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>' +
                            '<path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>' +
                        '</svg>' +
                        '<span class="mx-2"> ' + hour + ':' + min + ':' + sec + ' </span>' +
                    '</div>' +
                '</div>';
        }
        $(this).html(max_translation_deadline_text);
        $(this).css("visibility", "visible");
    });

    $(".process_completed_utc").each(function () {
        $(this).removeClass("process_completed_utc");
        var utcTime = $(this).text();
        utcTime = parseInt(utcTime) * 1000;
        var completed = new Date(utcTime);
        var m = completed.getMonth() + 1;
        if (m < 10) {
            m = "0" + m;
        }
        var d = completed.getDate();
        if (d < 10) {
            d = "0" + d;
        }
        var h = completed.getHours();
        if (h < 10) {
            h = "0" + h;
        }
        var mi = completed.getMinutes();
        if (mi < 10) {
            mi = "0" + mi;
        }
        $(this).html(
            " %s".replace(
                "%s",
                "<div class='d-flex align-items-center text-muted'>" +
                    "<div class='me-2'> Completed</div>" +
                    "<div class='fw-bold'>" +
                    completed.getFullYear() +
                    "-" +
                    m +
                    "-" +
                    d +
                    "</div>" +
                    "  " +
                    ` <svg xmlns="http://www.w3.org/2000/svg" class="mx-2" width="16" height="16" fill="#f89406" class="bi bi-clock" viewBox="0 0 16 16">
                    <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>
                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>
                  </svg> ` +
                    "<span class='fw-bold'>" +
                    h +
                    ":" +
                    mi +
                    ":00 " +
                    "</span>" +
                    "</div>"
            )
        );
        $(this).css("visibility", "visible");
    });
}

//Clickable actions on the homepage btns
$(document).ready(function () {
    $(".button_join").on("click", function (event) {
        event.preventDefault();
        //window.location.href = "/register";
        window.open("/register", "_self");
        return false;
    });
    $(".btn-home-slider").on("click", function (event) {
        event.preventDefault();
        //window.location.href = "/register";
        window.open("/register", "_self");
        return false;
    });
    $(".button").on("click", function (event) {
        event.preventDefault();
        //$("body").scrollTo("#learnmore");
        $("html, body").animate(
            {
                scrollTop: $("#learnmore").offset().top,
            },
            2000
        );
    });
});
