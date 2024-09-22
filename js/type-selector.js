document.querySelectorAll('.tipo-label').forEach(label => {
    label.addEventListener('click', function() {
        this.classList.toggle('selected');

        const selectedTypes = Array.from(document.querySelectorAll('.tipo-label.selected'))
            .map(label => label.getAttribute('data-tipo'));

        document.getElementById('tipos_seleccionados').value = selectedTypes.join(',');
    });
});