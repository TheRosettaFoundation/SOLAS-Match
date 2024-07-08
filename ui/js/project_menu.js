let menu = document.querySelector(".menu_open");
console.log(menu);
let menuList = document.querySelector(".menu_list");
console.log(menuList);

menu.addEventListener("click", function () {
    console.log("clicked");
    menuList.classList.toggle("d-none");
});
