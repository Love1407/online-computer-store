document.getElementById('signupForm').addEventListener('submit', function(e){
    var name = document.getElementById('name').value.trim();
    var email = document.getElementById('email').value.trim();
    var pass = document.getElementById('password').value;
    var confirm = document.getElementById('confirm_password').value;
    var emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var passReg = /^(?=.*[A-Za-z])(?=.*\d).{8,}$/;
    document.querySelectorAll('input').forEach(input => input.setCustomValidity(''));

    if(!name){ 
        showError('name', 'Please enter your full name');
        e.preventDefault(); 
        return; 
    }
    
    if(!emailReg.test(email)){ 
        showError('email', 'Please enter a valid email address');
        e.preventDefault(); 
        return; 
    }
    
    if(!passReg.test(pass)){ 
        showError('password', 'Password must be at least 8 characters with one letter and one number');
        e.preventDefault(); 
        return; 
    }
    
    if(pass !== confirm){ 
        showError('confirm_password', 'Passwords do not match');
        e.preventDefault(); 
        return; 
    }
});

function showError(fieldId, message) {
    var field = document.getElementById(fieldId);
    field.setCustomValidity(message);
    field.reportValidity();
    field.focus();
    field.addEventListener('input', function() {
        field.setCustomValidity('');
    }, { once: true });
}