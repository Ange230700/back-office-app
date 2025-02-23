document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('waste-container');
    const defaultOptions = container.getAttribute('data-options') || '<option value="">Sélectionner un type</option>';

    const wasteRowTemplate = document.createElement('template');
    wasteRowTemplate.innerHTML = `
        <div class="waste-item flex space-x-4 mb-2">
            <select name="type_dechet[]" class="w-full p-2 border border-gray-300 rounded-lg">
                ${defaultOptions}
            </select>
            <input type="number" min="0" step="0.1" name="quantite_kg[]" placeholder="Quantité (kg)" class="w-full p-2 border border-gray-300 rounded-lg">
            <button type="button" class="bg-cyan-950 remove-waste hover:bg-red-600 text-white px-2 py-1 rounded">
                Supprimer
            </button>
        </div>
    `.trim();

    function updateWasteSelectOptions() {
        const selects = document.querySelectorAll("select[name='type_dechet[]']");
        const selectedValues = Array.from(selects)
            .map(select => select.value)
            .filter(value => value !== "");

        selects.forEach(select => {
            select.querySelectorAll("option").forEach(option => {
                if (selectedValues.includes(option.value) && option.value !== select.value) {
                    option.disabled = true;
                } else {
                    option.disabled = false;
                }
            });
        });
    }

    container.addEventListener('change', function (e) {
        if (e.target?.matches("select[name='type_dechet[]']")) {
            updateWasteSelectOptions();
        }
    });

    document.getElementById('add-waste').addEventListener('click', function () {
        container.insertAdjacentHTML('beforeend', wasteRowTemplate.innerHTML);
        updateWasteSelectOptions();
    });

    container.addEventListener('click', function (e) {
        if (e.target?.matches('button.remove-waste')) {
            e.target.parentNode.remove();
            updateWasteSelectOptions();
        }
    });
});
