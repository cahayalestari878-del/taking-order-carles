document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-auto-dismiss]').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity .4s ease, transform .4s ease';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-4px)';
            setTimeout(() => el.remove(), 400);
        }, 4500);
    });

    const fileInput = document.querySelector('input[type="file"][name="gambar"]');
    if (fileInput) {
        const preview = document.createElement('img');
        preview.className = 'w-24 h-24 object-cover rounded-lg mt-2 hidden border border-[#e2e8df]';
        fileInput.parentNode.appendChild(preview);
        fileInput.addEventListener('change', e => {
            const file = e.target.files[0];
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
            }
        });
    }

    const toggle = document.querySelector('[data-toggle-sidebar]');
    const sidebar = document.querySelector('[data-sidebar]');
    if (toggle && sidebar) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('fixed');
            sidebar.classList.toggle('inset-y-0');
            sidebar.classList.toggle('left-0');
            sidebar.classList.toggle('z-40');
        });
    }
});