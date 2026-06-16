// ============================================
// RezwanMart — Main JavaScript
// ============================================

document.addEventListener('DOMContentLoaded', function () {

    // ---- Auto-hide alerts after 4 seconds ----
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });

    // ---- Client-side Registration Validation ----
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            let valid = true;

            const fullName = document.getElementById('full_name');
            const email    = document.getElementById('email');
            const password = document.getElementById('password');
            const confirm  = document.getElementById('confirm_password');
            const phone    = document.getElementById('phone');

            clearErrors();

            if (!fullName.value.trim() || fullName.value.trim().length < 3) {
                showError(fullName, 'Full name must be at least 3 characters.');
                valid = false;
            }

            if (!isValidEmail(email.value.trim())) {
                showError(email, 'Please enter a valid email address.');
                valid = false;
            }

            if (password.value.length < 6) {
                showError(password, 'Password must be at least 6 characters.');
                valid = false;
            }

            if (password.value !== confirm.value) {
                showError(confirm, 'Passwords do not match.');
                valid = false;
            }

            if (phone && phone.value && !/^[0-9+\-\s]{7,15}$/.test(phone.value)) {
                showError(phone, 'Please enter a valid phone number.');
                valid = false;
            }

            if (!valid) e.preventDefault();
        });
    }

    // ---- Client-side Login Validation ----
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            let valid = true;
            clearErrors();

            const email    = document.getElementById('email');
            const password = document.getElementById('password');

            if (!isValidEmail(email.value.trim())) {
                showError(email, 'Please enter a valid email address.');
                valid = false;
            }

            if (!password.value) {
                showError(password, 'Password is required.');
                valid = false;
            }

            if (!valid) e.preventDefault();
        });
    }

    // ---- Quantity Controls (Cart) ----
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = this.parentElement.querySelector('.qty-input');
            let val = parseInt(input.value) || 1;
            if (this.dataset.action === 'increase') val = Math.min(val + 1, 99);
            if (this.dataset.action === 'decrease') val = Math.max(val - 1, 1);
            input.value = val;
        });
    });

    // ---- Search Bar (Filter products) ----
    const searchInput = document.getElementById('productSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.product-card').forEach(card => {
                const name = card.querySelector('.product-name')?.textContent.toLowerCase() || '';
                card.style.display = name.includes(query) ? '' : 'none';
            });
        });
    }

    // ---- Admin: Confirm Delete ----
    document.querySelectorAll('.confirm-delete').forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });

    // ---- Password Toggle ----
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function () {
            const target = document.getElementById(this.dataset.target);
            if (!target) return;
            if (target.type === 'password') {
                target.type = 'text';
                this.textContent = '🙈';
            } else {
                target.type = 'password';
                this.textContent = '👁️';
            }
        });
    });

    // ---- Image Preview (Admin: Add Product) ----
    const imageInput = document.getElementById('productImage');
    const imagePreview = document.getElementById('imagePreview');
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }

});

// ---- Helpers ----
function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function showError(input, message) {
    input.classList.add('error');
    const err = document.createElement('div');
    err.className = 'form-error';
    err.innerHTML = '⚠ ' + message;
    input.parentElement.appendChild(err);
}

function clearErrors() {
    document.querySelectorAll('.form-error').forEach(e => e.remove());
    document.querySelectorAll('.form-control.error').forEach(e => e.classList.remove('error'));
}
