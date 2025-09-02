//  Subscription start date picker on organisation public profile page
//  ---------------------------------------------------------------

// Hidden + visible inputs
const dataOrgSubHiddenInput = document.getElementById("start_date_sub");

const dataOrgSubVisibleInput = document.getElementById("datetimepicker2Input");

// Pre-fill visible input with local time if hidden has UTC
if (dataOrgSubHiddenInput.value) {
    const localTime = dayjs.utc(dataOrgSubHiddenInput.value).local();
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

// Sync changes from visible → hidden in UTC

if (dataOrgSubHiddenInput) {
    dataOrgSubVisibleInput.addEventListener("change", (e) => {
        const local = dayjs(e.target.value);
        const utcTime = local.utc();
        dataOrgSubHiddenInput.value = utcTime.format("YYYY-MM-DD HH:mm:ss");
    });
}
