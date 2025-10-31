
window.addEventListener('scroll', () => {
  const header = document.querySelector('header');
  if (window.scrollY > 50) {
    header.style.backgroundColor = '#00c1ff';
  } else {
    header.style.backgroundColor = '#c8f9ff';
  }
});


function ajustarFuente() {
  const base = window.innerWidth < 600 ? 14 : 16;
  document.body.style.fontSize = base + 'px';
}
window.addEventListener('resize', ajustarFuente);
ajustarFuente();


const productos = document.querySelectorAll('.producto');
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = '1';
      entry.target.style.transform = 'translateY(0)';
    }
  });
}, { threshold: 0.2 });

productos.forEach(prod => {
  prod.style.opacity = '0';
  prod.style.transform = 'translateY(50px)';
  prod.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
  observer.observe(prod);
});
