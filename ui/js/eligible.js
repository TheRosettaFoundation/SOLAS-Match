console.log("testing if it is working");

const selectEligibles = document.querySelectorAll(".eligible");

selectEligibles.forEach(function (curr, index, arr) {
    let currentSelect = curr.querySelector("select");

    console.log(currentSelect);

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
