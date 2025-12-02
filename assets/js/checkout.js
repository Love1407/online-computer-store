const countrySelect = document.getElementById('country');
const stateSelect = document.getElementById('state');

countrySelect.addEventListener('change', function(){
    const states = countries[this.value] || [];
    stateSelect.innerHTML = '<option value="">Select state</option>';
    states.forEach(s => {
        const opt = document.createElement('option');
        opt.value = s;
        opt.textContent = s;
        stateSelect.appendChild(opt);
    });
});

document.querySelector('input[name="card_number"]')?.addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '');
});

document.querySelector('input[name="exp_date"]')?.addEventListener('input', function(e) {
    let val = this.value.replace(/\D/g, '');
    if (val.length >= 2) {
        this.value = val.slice(0, 2) + '/' + val.slice(2, 4);
    } else {
        this.value = val;
    }
});

document.querySelector('input[name="cvv"]')?.addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '');
});