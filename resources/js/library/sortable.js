import $ from "jquery";
import Sortable from "sortablejs";

$(document).ready(function () {
    const container = document.getElementById('sortable-accounts');
    if (container && container instanceof HTMLElement) {
        console.log('Sortable is working!');
        Sortable.create(container, {
            animation: 150,
            onEnd: function (evt) {
                const ids = [...container.children].map(el => el.dataset.id).filter(Boolean);

                fetch('/profile/accounts/order', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ ids })
                });
            }
        });
    }
});
