document.addEventListener("DOMContentLoaded", function () {
  const header = document.querySelector("header");
  const stickyPoint = header.offsetTop;

  window.addEventListener("scroll", function () {
    if (window.pageYOffset > stickyPoint) {
      header.classList.add("sticky");
    } else {
      header.classList.remove("sticky");
    }
  });
});
