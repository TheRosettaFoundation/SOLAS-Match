const selectEligibles = document.querySelectorAll("form .eligible");

async function setEligibility({ sc, sl, tc, tl, el, user, sesskey }) {
    let url = `/set_paid_eligible_pair/${user}/sl/${sl}/sc/${sc}/tl/${tl}/tc/${tc}/eligible/${el}/`;
    const key = { sesskey };
    try {
        const response = await fetch(url, {
            method: "POST",
            body: new URLSearchParams(key),
        });

        if (!response.ok) {
            throw new Error("error");
        }
    } catch (error) {
        console.error(error);
    }
}

selectEligibles.forEach(function (curr, index, arr) {
    let codes = {};
    curr.addEventListener("change", function (e) {
        e.preventDefault();
        let parent = curr.parentElement;
        let sc = parent.querySelector(".sc").textContent;
        let sl = parent.querySelector(".sl").textContent;
        let tl = parent.querySelector(".tl").textContent;
        let tc = parent.querySelector(".tc").textContent;
        let user = parent.querySelector(".user").textContent;
        let sesskey = parent.querySelector(".sesskey").textContent;
        let el = curr.value;

        codes = {
            sc,
            sl,
            tl,
            tc,
            el,
            user,
            sesskey,
        };

        setEligibility(codes);
    });
});
