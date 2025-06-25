document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar_container');
    const navLinks = document.querySelectorAll('.sidebar_nav-item');

    if (sidebar) {
        // Expandir cuando el mouse entra
        sidebar.addEventListener('mouseenter', () => {
            sidebar.classList.add('is-expanded');
        });

        // Contraer cuando el mouse sale
        sidebar.addEventListener('mouseleave', () => {
            sidebar.classList.remove('is-expanded');
        });
    }

    // Contraer cuando se hace clic en un enlace
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            sidebar.classList.remove('is-expanded');
        });
    });
});