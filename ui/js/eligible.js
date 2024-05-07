console.log("testing if it is working");

const selectEligibles = document.querySelectorAll(".eligible");

console.log(selectEligibles);

selectEligibles.forEach(function (curr, index, arr) {
    console.log(curr);

    let sc = curr.querySelector(".sc");

    console.log(sc);

    currentSelect.addEventListener("change", function () {
        let eligibleCodes =
            currentSelect.previousElementSibling("eligible_codes");
        let sc = eligibleCodes.querySelector(".sc");
        console.log("sc in on change");
        console.log(sc);
    });
});
