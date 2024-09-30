document.addEventListener('DOMContentLoaded', function() {
    const selectedTypesInput = document.getElementById('tipos_seleccionados');

    const initialSelectedTypes = Array.from(document.querySelectorAll('.tipo-label.selected'))
        .map(label => label.getAttribute('data-tipo'));
    selectedTypesInput.value = initialSelectedTypes.join(',');

    document.querySelectorAll('.tipo-label').forEach(label => {
        label.addEventListener('click', function() {
            this.classList.toggle('selected');

            const selectedTypes = Array.from(document.querySelectorAll('.tipo-label.selected'))
                .map(label => label.getAttribute('data-tipo'));

            selectedTypesInput.value = selectedTypes.join(',');
        });
    });
});

