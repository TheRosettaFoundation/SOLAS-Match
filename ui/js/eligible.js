console.log("testing if it is working");

const selectEligibles = document.querySelectorAll(".eligible");

console.log(selectEligibles);

selectEligibles.forEach(function (curr, index, arr) {
    console.log(curr);

    let sc = curr.firstChild;

    console.log(sc);

    curr.addEventListener("change", function () {
        let eligibleCodes = curr.previousElementSibling("eligible_codes");

        console.log(eligibleCodes);

        let sc = eligibleCodes.querySelector(".sc");
        console.log("sc in on change");
        console.log(sc);
    });
});
