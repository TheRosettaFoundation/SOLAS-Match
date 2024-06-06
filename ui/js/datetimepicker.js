var dUTC = $("#deadline_field").val();

// Parse the UTC time using dayjs
const dayjs = window.dayjs;
dayjs.extend(window.dayjs_plugin_utc);

var parsedUTC = dayjs.utc(dUTC);

// Convert the parsed UTC time to local time
let localTime = parsedUTC.local();

let domi = document.getElementById("datetimepicker1Input");

domi.setAttribute("value", localTime.format("YYYY-MM-DD HH:mm:ss"));

const datetimepicker1 = new tempusDominus.TempusDominus(
    document.getElementById("datetimepicker1"),
    {
        //put your config here
        display: {
            components: {
                year: true,
                month: true,
                date: true,
                hours: true,
                minutes: true,
                seconds: true,
            },
        },
        useCurrent: false,

        localization: {
            format: "yyyy-MM-dd HH:mm:ss",
            locale: "us",
        },
    }
);

let deadline = document.getElementById("deadline_field");

document
    .getElementById("datetimepicker1Input")
    .addEventListener("change", (e) => {
        let local = dayjs(e.target.value);
        console.log(local);
        let utcTime = dayjs.utc(local);
        deadline.setAttribute("value", utcTime.format("YYYY-MM-DD HH:mm:ss"));
    });
