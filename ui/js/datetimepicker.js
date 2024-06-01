var dUTC = $("#deadline_field").val();
const date = new Date(dUTC);
const dayjs = window.dayjs;
dayjs.extend(window.dayjs_plugin_utc);

const test = dayjs(date)
    .utc("z")
    .local()
    .format("YYYY-MM-DD HH:mm:ss")
    .toString();

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
