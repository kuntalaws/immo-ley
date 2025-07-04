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


  const submitInput = document.querySelector('.wpcf7 input[type="submit"]');
  if (submitInput) {
    const button = document.createElement('button');
    button.type = 'submit';
    button.innerText = 'Versturen';
    button.className = submitInput.className;
    submitInput.parentNode.replaceChild(button, submitInput);
  }
});
