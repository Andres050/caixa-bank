let clock = document.getElementById('clock');

if (clock) {
    function updateClock() {
        const ahora = new Date();
        const horas = String(ahora.getHours()).padStart(2, '0');
        const minutos = String(ahora.getMinutes()).padStart(2, '0');
        const segundos = String(ahora.getSeconds()).padStart(2, '0');

        clock.textContent = `${horas}:${minutos}:${segundos}`;
    }

    setInterval(updateClock, 1000);
    updateClock();
}
