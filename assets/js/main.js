document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.querySelector('.nav-toggle');
    var links = document.querySelector('.nav-links');

    if (toggle && links) {
        toggle.addEventListener('click', function () {
            links.classList.toggle('abierto');
            var expandido = links.classList.contains('abierto');
            toggle.setAttribute('aria-expanded', expandido ? 'true' : 'false');
        });
    }
});
