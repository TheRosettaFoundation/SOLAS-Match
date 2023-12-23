const print = document.getElementById("print");
const iframe = document.getElementById("iframe");
const iframeWindow = iframe.contentWindow;

console.log(`iframe working`);

print.addEventListener("click", (e) => {
    e.preventDefault();
    iframeWindow.print();
});
