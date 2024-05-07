// const selectEligibles = document.querySelectorAll("form .eligible");

const forms = document.querySelectorAll(".eligible");
console.log(forms);

forms.forEach(function (curr, index, arr) {
    let selectElement = curr.querySelector("select");
    console.log(selectElement);
    selectElement.addEventListener("change", function (e) {
        e.preventDefault();
        curr.addEventListener("submit", function (e) {
            e.preventDefault();
        });
        curr.submit();
    });
});

// async function setEligibility({ sc, sl, tc, tl, el, user, key }) {
//     let url = `/set_paid_eligible_pair/${user}/sl/${sl}/sc/${sc}/tl/${tl}/tc/${tc}/eligible/${el}/`;

//     console.log(url);
//     console.log(key);

//     let data = { sesskey: key };

//     console.log(data);

//     const response = await fetch(url, {
//         method: "POST",
//         body: JSON.stringify(data),
//         headers: {
//             "Content-Type": "application/json",
//         },
//     });

//     console.log(response);
// }

// selectEligibles.forEach(function (curr, index, arr) {
//     let codes = {};
//     curr.addEventListener("change", function (e) {
//         e.preventDefault();
//         let previousSibli = curr.parentElement.previousElementSibling;

//         let sc = previousSibli.querySelector(".sc").textContent;
//         let sl = previousSibli.querySelector(".sl").textContent;
//         let tl = previousSibli.querySelector(".tl").textContent;
//         let tc = previousSibli.querySelector(".tc").textContent;
//         let user = previousSibli.querySelector(".user").textContent;
//         let key = previousSibli.querySelector(".key").textContent;
//         console.log(key);
//         let el = curr.value;

//         codes = {
//             sc,
//             sl,
//             tl,
//             tc,
//             el,
//             user,
//             key,
//         };

//         console.log(codes);

//         setEligibility(codes);
//     });
// });
