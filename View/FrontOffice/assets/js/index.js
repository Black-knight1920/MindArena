function scrollToSection(href) {
  let target = document.querySelector(href);
  if (target) {
    target.scrollIntoView({
      behavior: 'smooth',
      block: 'start'
    });
  }
}

function updateActiveMenu() {
  let sections = document.querySelectorAll('section[id]');
  let scrollY = window.pageYOffset;

  for (let i = 0; i < sections.length; i++) {
    let current = sections[i];
    let sectionHeight = current.offsetHeight;
    let sectionTop = current.offsetTop - 100;
    let sectionId = current.getAttribute('id');
    let menuItem = document.querySelector('.diagonal-item[href="#' + sectionId + '"]');

    if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
      let allItems = document.querySelectorAll('.diagonal-item');
      for (let j = 0; j < allItems.length; j++) {
        allItems[j].classList.remove('active');
      }
      if (menuItem) {
        menuItem.classList.add('active');
      }
    }
  }
}

window.addEventListener('scroll', updateActiveMenu);

