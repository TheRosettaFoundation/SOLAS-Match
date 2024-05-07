console.log("testing if it is working");

const selectEligibles = document.querySelectorAll("form .eligible");
console.log(selectEligibles);

selectEligibles.forEach(function (curr, index, arr) {
    let codes = {};
    curr.addEventListener("change", function (e) {
        e.preventDefault();
        console.log("changed");
        let previousSibli = curr.parentElement.previousElementSibling;

        let sc = previousSibli.querySelector(".sc").textContent;
        let sl = previousSibli.querySelector(".sl").textContent;
        let tl = previousSibli.querySelector(".tl").textContent;
        let tc = previousSibli.querySelector(".tc").textContent;

        codes = {
            sc,
            sl,
            tl,
            tc,
        };

        console.log(codes);
    });
});

// selectEligibles.forEach(function (curr, index, arr) {
//     console.log(curr);

//     let sc = curr.querySelector(".eligible_codes");

//     console.log(sc);

//     curr.addEventListener("change", function () {
//         console.log("current");
//     });
// });
