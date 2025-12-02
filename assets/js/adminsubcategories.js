function loadCategories(gid, selectedCat = null) {
    const catEl = document.getElementById('category_id');
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');

    if (!gid) {
        if (catEl) {
            catEl.innerHTML = "<option value=''>First, select a group above ↑</option>";
            catEl.disabled = true;
        }
        if (step2 && step2.classList) step2.classList.remove('adm-step-active', 'adm-step-completed');
        if (step1 && step1.classList) step1.classList.add('adm-step-active');
        return;
    }

    if (step1 && step1.classList) {
        step1.classList.add('adm-step-completed');
        step1.classList.remove('adm-step-active');
    }
    if (step2 && step2.classList) step2.classList.add('adm-step-active');
    catEl.classList.add('loading');
    catEl.innerHTML = "<option value=''>⏳ Loading categories...</option>";
    catEl.disabled = true;

    fetch('adminsubcategories.php?fetch_categories=' + encodeURIComponent(gid))
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            let html = "<option value=''>-- Choose a Category --</option>";
            
            if (data.length === 0) {
                html = "<option value=''>⚠️ No categories available for this group</option>";
            } else {
                data.forEach(row => {
                    const sel = (selectedCat != null && String(selectedCat) === String(row.id)) ? ' selected' : '';
                    html += `<option value="${row.id}"${sel}>${row.category_name}</option>`;
                });
            }
            
            if (catEl) {
                catEl.innerHTML = html;
                catEl.classList.remove('loading');
                catEl.disabled = false;
            }
            if (data.length > 0) {
                if (step2 && step2.classList) {
                    step2.classList.add('adm-step-completed');
                    step2.classList.remove('adm-step-active');
                }
                if (step3 && step3.classList) step3.classList.add('adm-step-active');
            }
        })
        .catch(err => {
            console.error('Error loading categories:', err);
            if (catEl) {
                catEl.innerHTML = "<option value=''> Error loading categories</option>";
                catEl.classList.remove('loading');
                catEl.disabled = false;
            }
            showNotification('Failed to load categories. Please try again.', 'error');
        });
}

document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_id');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('step2').classList.add('adm-step-completed');
                document.getElementById('step2').classList.remove('adm-step-active');
                document.getElementById('step3').classList.add('adm-step-active');
            }
        });
    }

    const subcategoryInput = document.getElementById('subcategory_name');
    if (subcategoryInput) {
        subcategoryInput.addEventListener('input', function() {
            if (this.value.trim()) {
                document.getElementById('step3').classList.add('adm-step-completed');
            } else {
                document.getElementById('step3').classList.remove('adm-step-completed');
            }
        });
    }
});

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `adm-alert adm-alert-${type === 'error' ? 'danger' : type}`;
    notification.innerHTML = `
        <span class="adm-alert-icon">${type === 'error' ? '⚠️' : 'ℹ️'}</span>
        <span>${message}</span>
    `;
    
    const messagesContainer = document.querySelector('.adm-messages') || (() => {
        const container = document.createElement('div');
        container.className = 'adm-messages';
        document.querySelector('.adm-content').insertBefore(container, document.querySelector('.adm-card'));
        return container;
    })();
    
    messagesContainer.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transition = 'all 0.5s ease';
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-20px)';
        setTimeout(() => notification.remove(), 500);
    }, 5000);
}