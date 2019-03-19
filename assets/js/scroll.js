window.addEventListener('scroll', (event) => {
    const header = document.getElementById('header');

    if (window.scrollY > 72 && !header.classList.contains('scroll')) {
        header.classList.add('scroll');
    } else if (window.scrollY <= 72 && header.classList.contains('scroll')) {
        header.classList.remove('scroll');
    }
});
