//  Task date picker on task edit page
//  ---------------------------------------------------------------

var dUTC = $("#deadline_field").val();

// Parse the UTC time using dayjs
const dayjs = window.dayjs;
dayjs.extend(window.dayjs_plugin_utc);

var parsedUTC = dayjs.utc(dUTC);

// Convert the parsed UTC time to local time
let localTime = parsedUTC.local();

let domi = document.getElementById("datetimepicker1Input");

domi.setAttribute("value", localTime.format("YYYY-MM-DD HH:mm:ss"));

dataOrgSub.setAttribute("value", localTime.format("YYYY-MM-DD HH:mm:ss"));

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
            locale: "en-UK",
        },
    }
);

let deadline = document.getElementById("deadline_field");

document
    .getElementById("datetimepicker1Input")
    .addEventListener("change", (e) => {
        let local = dayjs(e.target.value);
        let utcTime = dayjs.utc(local);
        deadline.setAttribute("value", utcTime.format("YYYY-MM-DD HH:mm:ss"));
    });

//  Subscription start date picker on organisation public profile page
//  ---------------------------------------------------------------

// Date input for subscription start date - org page
let dataOrgSubHiddenInput = document.getElementById("start_date_field");
let dataOrgSubVisibleInput = document.getElementById("datetimepicker2Input");
// Convert hidden UTC value → local and show in picker
if (dataOrgSubHiddenInput.value) {
    const localTime = dayjs.utc(hiddenField.value).local();
    dataOrgSubVisibleInput.value = localTime.format("YYYY-MM-DD HH:mm:ss");
}

const datetimepicker2 = new tempusDominus.TempusDominus(
    document.getElementById("datetimepicker2"),
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
            locale: "en-UK",
        },
    }
);
// Sync picker changes back to hidden UTC field
dataOrgSubVisibleInput.addEventListener("change", (e) => {
    const local = dayjs(e.target.value);
    const utcTime = local.utc();
    dataOrgSubHiddenInput.value = utcTime.format("YYYY-MM-DD HH:mm:ss");
});
