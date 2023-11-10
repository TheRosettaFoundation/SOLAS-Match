var parameters; // Instance of Parameters Class holding data retrieved from Server (e.g. Translations)
var parametersLoaded = false;

// Passed from PHP
var siteLocation;

// http://infiniteajaxscroll.com
var ias = $.ias({
    container: ".ts",
    item: ".ts-task",
    pagination: "#ias-pagination",
    next: ".ts-next a",
});

ias.extension(new IASSpinnerExtension());
ias.extension(new IASTriggerExtension({ offset: 100 }));

ias.on("rendered", renderTaskDetails);

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
    console.log(parameters.getTranslation("common_due_by"));
}

function loadingComplete() {
    parametersLoaded = true;

    renderTaskDetails();

    document.getElementById("loading_warning").innerHTML = "";
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
            parameters
                .getTranslation("common_due_by")
                .replace(
                    "%s",
                    deadline.getFullYear() +
                        "-" +
                        m +
                        "-" +
                        d +
                        " " +
                        h +
                        ":" +
                        mi +
                        ":00"
                ) +
                " " +
                Intl.DateTimeFormat().resolvedOptions().timeZone
        );

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
            "Completed <strong>%s</strong>".replace(
                "%s",
                completed.getFullYear() +
                    "-" +
                    m +
                    "-" +
                    d +
                    " " +
                    h +
                    ":" +
                    mi +
                    ":00"
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
