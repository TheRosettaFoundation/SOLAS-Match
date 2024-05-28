//importing the css and fontawesome like this is not required
//you can link to it instead.
import "@eonasdan/tempus-dominus/dist/css/tempus-dominus.css";
import { library, dom } from "@fortawesome/fontawesome-svg-core";
import { fas } from "@fortawesome/free-solid-svg-icons";
library.add(fas);
dom.watch();
//Bootstrap is not required for the picker to work
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";
import { TempusDominus, version, DateTime } from "@eonasdan/tempus-dominus";

//using "window" is just for the stackblitz, you do not need to do this
window.datetimepicker1 = new TempusDominus(
    document.getElementById("datetimepicker1"),
    {
        localization: {
            locale: "pt-BR",
            format: "dd/MM/yyyy HH:mm",
        },
    }
);

document.getElementById("change").addEventListener("click", () => {
    datetimepicker1.updateOptions({
        localization: {
            locale: document.getElementById("locale").value,
            format: document.getElementById("format").value,
        },
    });
    datetimepicker1.dates.setValue(new DateTime());
});

document.getElementById(
    "info"
).innerHTML = `Your browser's locale is ${navigator.language}.<br/>
You are using version ${version}`;
