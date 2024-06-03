// var dUTC = $("#deadline_field").val();
// var date = new Date(dUTC).toUTCString();

// console.log(dUTC);

// const dayjs = window.dayjs;
// dayjs.extend(window.dayjs_plugin_utc);

// var parsed = dayjs(dUTC);

// console.log(parsed);
// let parsedT = dayjs.utc(date);
// let local = parsedT.local();

// console.log(parsedT);
// console.log(local);
// const utcFrom = local.utc();
// console.log(utcFrom);

// const test = dayjs(dUTC).utc(true).local().format("YYYY-MM-DD HH:mm:ss");

// console.log(test);

// const testUTC = test.utc();

// console.log(testUTC);

// let domi = document.getElementById("datetimepicker1Input");

var dUTC = $("#deadline_field").val();
console.log("Original UTC time:", dUTC);

// Parse the UTC time using dayjs
const dayjs = window.dayjs;
dayjs.extend(window.dayjs_plugin_utc);

var parsedUTC = dayjs.utc(dUTC);
console.log("Parsed UTC time:", parsedUTC.format());

// Convert the parsed UTC time to local time
let localTime = parsedUTC.local();
console.log("Converted to local time:", localTime.format(""));

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

        localization: {
            format: "yyyy-MM-dd HH:mm:ss",
        },
    }
);

let deadline = document.getElementById("deadline_field");
console.log(deadline);

document
    .getElementById("datetimepicker1Input")
    .addEventListener("change", (e) => {
        let local = dayjs(e.target.value);
        // No need to call utc() on the local object itself
        // Convert to UTC directly with dayjs.utc()
        let utcTime = dayjs.utc(local);
        console.log(local.format());
        console.log(utcTime.format("YYYY-MM-DD HH:mm:ss"));
        deadline.value = utcTime.format("YYYY-MM-DD HH:mm:ss");
        console.log(deadline);
    });
