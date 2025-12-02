document.querySelectorAll('.toggle-items-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const orderId = btn.dataset.orderId;
        const itemsRow = document.getElementById('order-items-' + orderId);
        if (itemsRow.style.display === 'table-row') {
            itemsRow.style.display = 'none';
            btn.textContent = 'View Details';
        } else {
            itemsRow.style.display = 'table-row';
            btn.textContent = 'Hide Details';
        }
    });
});