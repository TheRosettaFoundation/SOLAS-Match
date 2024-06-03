var dUTC = $("#deadline_field").val();

console.log(dUTC);

const dayjs = window.dayjs;
dayjs.extend(window.dayjs_plugin_utc);
dayjs.extend(window.dayjs_plugin_timezone);
const dateUTCL = dayjs.local(dUTC);

const test = dayjs(dUTC).utc().local().format("YYYY-MM-DD HH:mm:ss").toString();

console.log(test);

let domi = document.getElementById("datetimepicker1Input");

domi.setAttribute("value", test);

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

        localization: {
            format: "yyyy-MM-dd HH:mm:ss",
        },
    }
);

let utc = document.getElementById("deadline_field");

document
    .getElementById("datetimepicker1Input")
    .addEventListener("change", (e) => {
        console.log(e.target.value);
        let local = dayjs.local(e.target.value);
        let newDateUtc = dayjs.tz("Coordinated Universal Time");
        utc.value = newDateUtc;
        console.log(newDateUtc);
    });
