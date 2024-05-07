const selectEligibles = document.querySelectorAll("form .eligible");

async function setEligibility({ sc, sl, tc, tl, el, user }) {
    let url = `/set_paid_eligible_pair/${user}/sl/${sl}/sc/${sc}/tl/${tl}/tc/${tc}/eligible/${el}/`;

    console.log(url);
    // const response = await fetch();
}

selectEligibles.forEach(function (curr, index, arr) {
    let codes = {};
    curr.addEventListener("change", function (e) {
        e.preventDefault();
        let previousSibli = curr.parentElement.previousElementSibling;

        let sc = previousSibli.querySelector(".sc").textContent;
        let sl = previousSibli.querySelector(".sl").textContent;
        let tl = previousSibli.querySelector(".tl").textContent;
        let tc = previousSibli.querySelector(".tc").textContent;
        let user = previousSibli.querySelector(".user").textContent;
        let el = curr.value;

        codes = {
            sc,
            sl,
            tl,
            tc,
            el,
            user,
        };

        console.log(codes);
        setEligibility(codes);
    });
});
