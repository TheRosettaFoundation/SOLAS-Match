const print = document.getElementById("print");
const iframe = document.getElementById("iframe");
const iframeWindow = iframe.contentWindow;

console.log(`iframe working`);
console.log(print);

print.addEventListener("click", (e) => {
    e.preventDefault();
    console.log("clicked");
    iframeWindow.print();
});
