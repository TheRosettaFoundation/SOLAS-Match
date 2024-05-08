// const selectEligibles = document.querySelectorAll("form .eligible");

const forms = document.querySelectorAll(".eligible");
console.log(forms);

forms.forEach(function (curr, index, arr) {
    let selectElement = curr.querySelector("select");
    let sc = curr.querySelector(".sc").textContent;
    let sl = curr.querySelector(".sl").textContent;
    let tc = curr.querySelector(".tc").textContent;
    let tl = curr.querySelector(".tl").textContent;
    let user = curr.querySelector(".user").textContent;

    console.log(sc, sl, tc, tl, user);
    console.log(selectElement);
    selectElement.addEventListener("change", function (e) {
        e.preventDefault();
    });
});

// async function setEligibility({ sc, sl, tc, tl, el, user, sesskey }) {
//     let url = `/set_paid_eligible_pair/${user}/sl/${sl}/sc/${sc}/tl/${tl}/tc/${tc}/eligible/${el}`;

//     console.log(url);
//     console.log(sesskey);

//     let data = { sesskey: sesskey };
//     console.log("data");
//     console.log(data);

//     try {
//         const response = await fetch(url, {
//             method: "POST",
//             headers: {
//                 "Content-Type": "application/json",
//             },
//             body: JSON.stringify(data),
//         });

//         if (!response.ok) {
//             throw new Error("error");
//         }

//         console.log(response);
//     } catch (error) {
//         console.error(error);
//     }
// }

// selectEligibles.forEach(function (curr, index, arr) {
//     let codes = {};
//     curr.addEventListener("change", function (e) {
//         e.preventDefault();
//         let previousSibli = curr.parentElement;
//         console.log(curr);
//         console.log(previousSibli);

//         let sc = previousSibli.querySelector(".sc").textContent;
//         let sl = previousSibli.querySelector(".sl").textContent;
//         let tl = previousSibli.querySelector(".tl").textContent;
//         let tc = previousSibli.querySelector(".tc").textContent;
//         let user = previousSibli.querySelector(".user").textContent;
//         let sesskey = previousSibli.querySelector(".sesskey").textContent;
//         console.log("sesskey", sesskey);

//         let el = curr.value;

//         codes = {
//             sc,
//             sl,
//             tl,
//             tc,
//             el,
//             user,
//             sesskey,
//         };

//         console.log(codes);

//         setEligibility(codes);
//     });
// });
