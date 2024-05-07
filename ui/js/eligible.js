const selectEligibles = document.querySelectorAll("form .eligible");

// const forms = document.querySelectorAll(".eligible");
// console.log(forms);

// forms.forEach(function (curr, index, arr) {
//     let selectElement = curr.querySelector("select");
//     console.log(selectElement);
//     selectElement.addEventListener("change", function (e) {
//         e.preventDefault();
//         curr.addEventListener("submit", function (e) {
//             e.preventDefault();
//         });
//         curr.submit();
//     });
// });

async function setEligibility({ sc, sl, tc, tl, el, user, sesskey }) {
    let url = `/set_paid_eligible_pair/${user}/sl/${sl}/sc/${sc}/tl/${tl}/tc/${tc}/eligible/${el}/`;

    console.log(url);
    console.log(sesskey);

    let data = {
        sesskey,
    };

    console.log(data);

    const response = await fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
    });

    console.log(response);
}

selectEligibles.forEach(function (curr, index, arr) {
    let codes = {};
    curr.addEventListener("change", function (e) {
        e.preventDefault();
        let previousSibli = curr.parentElement;
        console.log(curr);
        console.log(previousSibli);

        let sc = previousSibli.querySelector(".sc").textContent;
        let sl = previousSibli.querySelector(".sl").textContent;
        let tl = previousSibli.querySelector(".tl").textContent;
        let tc = previousSibli.querySelector(".tc").textContent;
        let user = previousSibli.querySelector(".user").textContent;
        let sesskey = previousSibli.querySelector(".sesskey").textContent;

        let el = curr.value;

        codes = {
            sc,
            sl,
            tl,
            tc,
            el,
            user,
            sesskey,
        };

        console.log(codes);

        setEligibility(codes);
    });
});
