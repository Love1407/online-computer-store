document.querySelectorAll('.adm-toggle-items-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const orderId = this.dataset.orderId;
        const itemsRow = document.getElementById('order-items-' + orderId);
        
        if (itemsRow.style.display === 'table-row') {
            itemsRow.style.display = 'none';
            this.innerHTML = 'View Items';
            this.classList.remove('adm-btn-secondary');
            this.classList.add('adm-btn-primary');
        } else {
            document.querySelectorAll('.adm-order-items-row').forEach(row => {
                row.style.display = 'none';
            });
            document.querySelectorAll('.adm-toggle-items-btn').forEach(otherBtn => {
                otherBtn.innerHTML = 'View Items';
                otherBtn.classList.remove('adm-btn-secondary');
                otherBtn.classList.add('adm-btn-primary');
            });
            itemsRow.style.display = 'table-row';
            this.innerHTML = 'Hide Items';
            this.classList.remove('adm-btn-primary');
            this.classList.add('adm-btn-secondary');
        }
    });
});

document.querySelectorAll('.adm-order-row').forEach(row => {
    row.addEventListener('click', function(e) {
        if (e.target.closest('.adm-toggle-items-btn')) return;
        
        const orderId = this.dataset.orderId;
        const btn = document.querySelector(`.adm-toggle-items-btn[data-order-id="${orderId}"]`);
        if (btn) btn.click();
    });
});