
'use strict';



/**
 * PRELOAD
 * 
 * loading will be end after document is loaded
 */

const preloader = document.querySelector("[data-preaload]");

window.addEventListener("load", function () {
  preloader.classList.add("loaded");
  document.body.classList.add("loaded");
});



/**
 * add event listener on multiple elements
 */

const addEventOnElements = function (elements, eventType, callback) {
  for (let i = 0, len = elements.length; i < len; i++) {
    elements[i].addEventListener(eventType, callback);
  }
}



/**
 * NAVBAR
 */

const navbar = document.querySelector("[data-navbar]");
const navTogglers = document.querySelectorAll("[data-nav-toggler]");
const overlay = document.querySelector("[data-overlay]");

const toggleNavbar = function () {
  navbar.classList.toggle("active");
  overlay.classList.toggle("active");
  document.body.classList.toggle("nav-active");
}

addEventOnElements(navTogglers, "click", toggleNavbar);



/**
 * HEADER & BACK TOP BTN
 */

const header = document.querySelector("[data-header]");
const backTopBtn = document.querySelector("[data-back-top-btn]");

let lastScrollPos = 0;

const hideHeader = function () {
  const isScrollBottom = lastScrollPos < window.scrollY;
  if (isScrollBottom) {
    header.classList.add("hide");
  } else {
    header.classList.remove("hide");
  }

  lastScrollPos = window.scrollY;
}

window.addEventListener("scroll", function () {
  if (window.scrollY >= 50) {
    header.classList.add("active");
    backTopBtn.classList.add("active");
    hideHeader();
  } else {
    header.classList.remove("active");
    backTopBtn.classList.remove("active");
  }
});



const heroSlider = document.querySelector("[data-hero-slider]");
const heroSliderItems = document.querySelectorAll("[data-hero-slider-item]");
const heroSliderPrevBtn = document.querySelector("[data-prev-btn]");
const heroSliderNextBtn = document.querySelector("[data-next-btn]");

let currentSlidePos = 0;
let lastActiveSliderItem = heroSliderItems[0];

const updateSliderPos = function () {
  lastActiveSliderItem.classList.remove("active");
  heroSliderItems[currentSlidePos].classList.add("active");
  lastActiveSliderItem = heroSliderItems[currentSlidePos];
}

const slideNext = function () {
  if (currentSlidePos >= heroSliderItems.length - 1) {
    currentSlidePos = 0;
  } else {
    currentSlidePos++;
  }

  updateSliderPos();
}

heroSliderNextBtn.addEventListener("click", slideNext);

const slidePrev = function () {
  if (currentSlidePos <= 0) {
    currentSlidePos = heroSliderItems.length - 1;
  } else {
    currentSlidePos--;
  }

  updateSliderPos();
}

heroSliderPrevBtn.addEventListener("click", slidePrev);

let autoSlideInterval;

const autoSlide = function () {
  autoSlideInterval = setInterval(function () {
    slideNext();
  }, 7000);
}

addEventOnElements([heroSliderNextBtn, heroSliderPrevBtn], "mouseover", function () {
  clearInterval(autoSlideInterval);
});

addEventOnElements([heroSliderNextBtn, heroSliderPrevBtn], "mouseout", autoSlide);

window.addEventListener("load", autoSlide);



const parallaxItems = document.querySelectorAll("[data-parallax-item]");

let x, y;

window.addEventListener("mousemove", function (event) {

  x = (event.clientX / window.innerWidth * 10) - 5;
  y = (event.clientY / window.innerHeight * 10) - 5;

  x = x - (x * 2);
  y = y - (y * 2);

  for (let i = 0, len = parallaxItems.length; i < len; i++) {
    x = x * Number(parallaxItems[i].dataset.parallaxSpeed);
    y = y * Number(parallaxItems[i].dataset.parallaxSpeed);
    parallaxItems[i].style.transform = `translate3d(${x}px, ${y}px, 0px)`;
  }

});


fetch('php/home.php')
  .then(response => response.json())
  .then(data => {
    if (data.error) {
      document.getElementById('user-info').style.display = 'none';
      document.getElementById('login-prompt').style.display = 'block';
    } else {
      document.getElementById('username').textContent = "WELCOME - " + data.name;
      document.getElementById('user-info').style.display = 'block';
      document.getElementById('login-prompt').style.display = 'none';
      if (data.usertype == 1) {
        document.getElementById('admin_dash').style.display = 'block';

      }

    }
  })
  .catch(error => console.error('Error fetching user data:', error));

// Logout function
function logout() {
  fetch('php/logout.php')
    .then(response => response.text())
    .then(data => {
      if (data.trim() === "logged_out") {
        window.location.href = 'home.html';
      }
    })
    .catch(error => console.error('Error during logout:', error));
}

document.getElementById('reservation').addEventListener('submit', function (event) {
  event.preventDefault();

  const name = document.querySelector('input[name="name"]').value;
  const phone = document.querySelector('input[name="phone"]').value;
  const date = document.querySelector('input[name="reservation-date"]').value;
  const time = document.querySelector('input[name="time"]').value;
  const person = document.querySelector('input[name="person"]').value;
  const message = document.querySelector('textarea[name="message"]').value;

  if (name && phone && date && time && person && message) {
    fetch('php/reservation.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: `name=${encodeURIComponent(name)}&phone=${encodeURIComponent(phone)}&date=${encodeURIComponent(date)}
    &time=${encodeURIComponent(time)}&person=${encodeURIComponent(person)}&message=${encodeURIComponent(message)}`
    })
      .then(response => response.text())
      .then(data => {
        if (data.trim() === 'ok') {
          Swal.fire({
            title: 'Reservation Done!',
            text: 'Your reservation was successfully placed.',
            icon: 'success',
            confirmButtonText: 'OK'
          }).then(() => {
            document.querySelector('input[name="name"]').value = '';
            document.querySelector('input[name="phone"]').value = '';
            document.querySelector('input[name="reservation-date"]').value = '';
            document.querySelector('input[name="time"]').value = '';
            document.querySelector('input[name="person"]').value = '';
            document.querySelector('textarea[name="message"]').value = '';
          });
        } else {
          Swal.fire({
            title: 'Invalid Data',
            text: 'There was an issue with your submission.',
            icon: 'error',
            confirmButtonText: 'Try Again'
          });
        }

      })
      .catch(error => {
        console.error('Error:', error);
      });
  } else {
    Swal.fire({
      title: 'Invalid Data',
      text: 'Plese fill the data.',
      icon: 'error',
      confirmButtonText: 'Try Again'
    });
  }

});


