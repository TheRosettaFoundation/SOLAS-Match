const selectEligibles = document.querySelectorAll("form .eligible");

selectEligibles.forEach(function (curr, index, arr) {
    let codes = {};
    curr.addEventListener("change", function (e) {
        e.preventDefault();
        let previousSibli = curr.parentElement.previousElementSibling;

        let sc = previousSibli.querySelector(".sc").textContent;
        let sl = previousSibli.querySelector(".sl").textContent;
        let tl = previousSibli.querySelector(".tl").textContent;
        let tc = previousSibli.querySelector(".tc").textContent;
        let el = curr.value;

        codes = {
            sc,
            sl,
            tl,
            tc,
            el,
        };

        console.log(codes);
    });
});
