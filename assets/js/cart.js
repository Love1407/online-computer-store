document.getElementById('checkoutBtn')?.addEventListener('click', function() {
    window.location.href = 'checkout_handler.php';
});

document.addEventListener("click", (e) => {
    if (e.target.classList.contains("prt-checkout-btn")) {
        let btn = e.target;
        let ripple = document.createElement("span");
        ripple.classList.add("ripple");

        let rect = btn.getBoundingClientRect();
        ripple.style.left = `${e.clientX - rect.left}px`;
        ripple.style.top = `${e.clientY - rect.top}px`;

        btn.appendChild(ripple);
        setTimeout(() => ripple.remove(), 600);
    }
});