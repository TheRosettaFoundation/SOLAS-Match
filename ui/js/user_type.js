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

const array_user_types = [...user_types];

if (array_user_types.length > 0) {
    array_user_types.forEach(function (curr, index, array_user_types) {
        let codes = {};
        curr.addEventListener("change", function (e) {
            e.preventDefault();
            let parent = curr.parentElement;
            let user = parent.querySelector(".user_for_type").textContent;
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
