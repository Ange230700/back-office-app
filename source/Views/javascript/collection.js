// source\Views\javascript\collection.js

function waitForPageLoading() {
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('waste-container');

        const wasteRowTemplate = document.createElement('template');
        wasteRowTemplate.innerHTML = `
    <div class="waste-item flex space-x-4 mb-2">
        <input type="text" name="waste_type[]" list="wasteTypesList" placeholder="Sélectionner ou saisir un nouveau type" class="w-full p-2 border border-gray-300 rounded-lg">
        <input type="number" min="0" step="0.1" name="quantity_kg[]" placeholder="Quantité (kg)" class="w-full p-2 border border-gray-300 rounded-lg">
        <button type="button" class="bg-cyan-950 remove-waste hover:bg-red-600 text-white px-2 py-1 rounded">Supprimer</button>
    </div>
    `.trim();

        document.getElementById('add-waste').addEventListener('click', function () {
            container.insertAdjacentHTML('beforeend', wasteRowTemplate.innerHTML);
        });

        container.addEventListener('click', function (e) {
            if (e.target?.matches('button.remove-waste')) {
                e.target.parentNode.remove();
            }
        });
    });
}

export { waitForPageLoading };
