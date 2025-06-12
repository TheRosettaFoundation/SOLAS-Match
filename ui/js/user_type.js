const user_types = document.querySelectorAll("form .user_type");

async function set_user_type({ user, type, sesskey }) {
    let url = `/set_user_type/${user}/type/${type}/`;
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

const arr = [...user_types];

if (arr.length > 0) {
    arr.forEach(function (curr, index, arr) {
        let codes = {};
        curr.addEventListener("change", function (e) {
            e.preventDefault();
            let parent = curr.parentElement;
            let user = parent.querySelector(".user").textContent;
            let type = curr.value;
            let sesskey = parent.querySelector(".sesskey").textContent;

            codes = {
                user,
                type,
                sesskey,
            };
            set_user_type(codes);
        });
    });
}
