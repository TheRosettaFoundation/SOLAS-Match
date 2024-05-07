console.log("testing if it is working");

const selectEligibles = document.querySelectorAll(".eligible");

selectEligibles.forEach(function (curr, index, arr) {
    console.log(curr);
    let select = curr.querySelector("select");

    console.log(select);
});

// selectEligibles.forEach(function (curr, index, arr) {
//     console.log(curr);

//     let sc = curr.querySelector(".eligible_codes");

//     console.log(sc);

//     curr.addEventListener("change", function () {
//         console.log("current");
//     });
// });
