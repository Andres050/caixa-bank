import $ from "jquery";

$(document).ready(function () {
    console.log('Checking schedule...');

    setInterval(() => {
        checkSchedule();
    }, 60000); // 1 minute

    function checkSchedule() {
        fetch('/profile/schedule/check', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        }).then(response => response.json()).then(data => {
            if (data.update) {
                //window.location.reload();
            }
        });
    }
});
