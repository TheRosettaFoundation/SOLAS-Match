$(document).ready(documentReady);

function documentReady() {
    $(".convert_utc_to_local_deadline_day_mon_year").each(function () {
        $(this).removeClass("convert_utc_to_local_deadline_day_mon_year");
        var dUTC = $(this).text();
        var year    = dUTC.substring(0, 4);
        var month   = dUTC.substring(5, 7) -1;
        var day     = dUTC.substring(8, 10);
        var hour    = dUTC.substring(11, 13);
        var minutes = dUTC.substring(14, 16);
        var seconds = dUTC.substring(17, 19);
        var d = new Date(Date.UTC(year, month, day, hour, minutes, seconds));

        const formatter = new Intl.DateTimeFormat((new Intl.DateTimeFormat()).resolvedOptions().locale, {month: 'short', day: 'numeric', year: 'numeric'});

        $(this).html(formatter.format(d));

        $(this).css("visibility", "visible");
    });

    const count_external_clicks = document.querySelectorAll(".count_external_clicks");

    async function content_item_increment_views({ click_id, sesskey }) {
        let url = `/content_item_increment_views/${click_id}/`;
        const key = { sesskey };
        try {
            const response = await fetch(url, {
                method: "POST",
                body: new URLSearchParams(key),
            });

            if (!response.ok) {
                throw new Error("error");
            }
        } catch (error) {
            console.error(error);
        }
    }

    const array_count_external_clicks = [...count_external_clicks];

    if (array_count_external_clicks.length > 0) {
        array_count_external_clicks.forEach(function (curr, index, array_count_external_clicks) {
            let codes = {};
            curr.addEventListener("click", function (e) {
                let click_id = curr.getAttribute("click_id");
                let sesskey = curr.getAttribute("sesskey");
                codes = {
                    click_id,
                    sesskey,
                };
                content_item_increment_views(codes);
            });
        });
    }
}
