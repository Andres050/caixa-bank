import $ from "jquery";

$(document).ready(function () {
    const container = document.getElementById('month');

    container.addEventListener('change', function () {
        fetch('/month/' + container.value, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        }).then(
            response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    console.error('Failed to fetch data');
                }
            }
        ).catch(error => {
            console.error('Error:', error);
        });
    })
});
