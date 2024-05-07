console.log("testing if it is working");

const selectEligibles = document.querySelectorAll(".eligible");
console.log(selectEligibles);

selectEligibles.forEach(function (curr, index, arr) {
    console.log(curr);

    curr.addEventListener("click", function (e) {
        e.preventDefault();
        console.log("changed");
        let previousSibli = curr.previousElementSibling;
        console.log(previousSibli);
    });

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
