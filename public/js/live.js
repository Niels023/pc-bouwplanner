// Kiezen en verwijderen zonder de pagina te herladen. Zonder JavaScript werken dezelfde
// formulieren gewoon met een herlaadactie, deze code maakt het alleen sneller.
document.addEventListener('submit', async (event) => {
    const form = event.target;
    if (!form.matches('[data-live]')) {
        return;
    }

    event.preventDefault();

    try {
        const response = await fetch(form.action, { method: 'POST', body: new FormData(form) });
        const page = new DOMParser().parseFromString(await response.text(), 'text/html');

        for (const id of ['steps', 'parts', 'totals']) {
            document.getElementById(id).innerHTML = page.getElementById(id).innerHTML;
        }

        document.querySelector(`form[data-part="${form.dataset.part}"] button`).focus();
    } catch {
        form.submit();
    }
});

// Op een telefoon staan de filters anders boven alle kaartjes.
const filters = document.getElementById('filters');
if (filters && !filters.hasAttribute('data-keep-open') && window.matchMedia('(max-width: 760px)').matches) {
    filters.open = false;
}
