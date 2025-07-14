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

  document.getElementById('hamburger').addEventListener('click', function () {
    this.classList.toggle('nav-open');
    document.body.classList.toggle('scroll-lock');
    document.querySelector('.header-nav-wrap').classList.toggle('nav-open');
  });


  const isMobile = window.innerWidth <= 768;
  const heroBg = document.querySelector(".hero-banner-bg");
  const bgImage = isMobile 
    ? heroBg.getAttribute("data-bg-mobile")
    : heroBg.getAttribute("data-bg-desktop");
  heroBg.style.backgroundImage = `url(${bgImage})`;

  
});
