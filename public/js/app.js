document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-dependent-url]').forEach((parent) => {
        const child = document.getElementById(parent.dataset.child);
        if (!child) return;

        const initialValue = child.dataset.selected || '';
        const reportError = (error) => {
            console.error(error);
            child.innerHTML = '<option value="">Options could not be loaded</option>';
            child.disabled = true;
        };
        const loadOptions = async (parentValue, selectedValue = '') => {
            child.innerHTML = '<option value="">Select</option>';
            child.disabled = !parentValue;
            if (!parentValue) return;

            const response = await fetch(parent.dataset.dependentUrl.replace('__id__', parentValue), {
                headers: { Accept: 'application/json' },
            });
            if (!response.ok) throw new Error('Unable to load location options.');

            const options = await response.json();
            options.forEach((item) => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.name;
                option.selected = String(item.id) === String(selectedValue);
                child.append(option);
            });
            child.disabled = options.length === 0;
        };

        parent.addEventListener('change', () => loadOptions(parent.value).catch(reportError));
        if (parent.value) loadOptions(parent.value, initialValue).catch(reportError);
    });

    const toggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.dashboard-sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', () => sidebar.classList.toggle('is-open'));
        document.addEventListener('click', (event) => {
            if (sidebar.classList.contains('is-open') && !sidebar.contains(event.target) && !toggle.contains(event.target)) {
                sidebar.classList.remove('is-open');
            }
        });
    }

    document.querySelectorAll('[data-role-select]').forEach((select) => {
        const company = document.querySelector('[data-company-field]');
        if (!company) return;
        const update = () => { company.hidden = select.value !== 'employer'; };
        select.addEventListener('change', update);
        update();
    });
});
