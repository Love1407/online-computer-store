document.addEventListener('DOMContentLoaded', function() {
    const successAlerts = document.querySelectorAll('.adm-alert-success');
    successAlerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'all 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});

let formChanged = false;
const form = document.querySelector('.adm-form');
if (form) {
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('change', () => {
            formChanged = true;
        });
    });

    window.addEventListener('beforeunload', (e) => {
        if (formChanged && !form.dataset.submitted) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    form.addEventListener('submit', () => {
        form.dataset.submitted = 'true';
        formChanged = false;
    });
}