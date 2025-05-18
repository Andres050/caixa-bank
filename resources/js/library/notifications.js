document.addEventListener('DOMContentLoaded', () => {
    const alert = document.querySelector('.alert');
    if (alert) {
        alert.classList.add('show');

        setTimeout(() => {
            alert.classList.remove('show');
        }, 3000); // Ocultar después de 3 segundos
    }
});
