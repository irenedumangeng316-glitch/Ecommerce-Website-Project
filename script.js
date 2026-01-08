// Fixed Navbar on Scroll
const header = document.querySelector('header');
function fixedNavbar() {
  if (!header) return;
  header.classList.toggle('scrolled', window.scrollY > 0);
}
fixedNavbar();
window.addEventListener('scroll', fixedNavbar);

// Toggle Navbar Menu
const menuBtn = document.querySelector('#menu-btn');
const nav = document.querySelector('.navbar');
if (menuBtn && nav) {
  menuBtn.addEventListener('click', () => {
    nav.classList.toggle('active');
    const expanded = nav.classList.contains('active');
    menuBtn.setAttribute('aria-expanded', expanded);
  });
}

// Toggle User Box
const userBtn = document.querySelector('#user-btn');
const userBox = document.querySelector('.user-box');
if (userBtn && userBox) {
  userBtn.addEventListener('click', () => {
    userBox.classList.toggle('active');
    const expanded = userBox.classList.contains('active');
    userBtn.setAttribute('aria-expanded', expanded);
  });
}

// Close Update Form
const closeBtn = document.querySelector('#close-form');
const updateContainer = document.querySelector('.update-container');
if (closeBtn && updateContainer) {
  closeBtn.addEventListener('click', () => {
    updateContainer.style.display = 'none';
  });
}