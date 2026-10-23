export function renderAntennas(tbody, template, antennas) {
    tbody.replaceChildren(...antennas.map((antenna) => {
        const row = template.content.cloneNode(true);
        const isInterventionOngoing = antenna.latestIntervention?.isInterventionOngoing ?? false;
        const createButton = row.querySelector('[data-action=create]');
        const closeButton = row.querySelector('[data-action=close]');

        row.querySelector('[data-cell=id]').textContent = antenna.id;
        row.querySelector('[data-cell=city]').textContent = antenna.city;
        row.querySelector('[data-cell=status]').textContent = antenna.status;
        row.querySelector('[data-cell=latest-intervention]').textContent = antenna.latestIntervention
            ? JSON.stringify(antenna.latestIntervention, null, 2)
            : '';

        createButton.hidden = isInterventionOngoing || antenna.status === 'DOWN';
        createButton.dataset.antennaId = antenna.id;
        closeButton.hidden = !isInterventionOngoing;
        closeButton.dataset.interventionId = antenna.latestIntervention?.id ?? '';
        closeButton.dataset.technicianIdentity = antenna.latestIntervention?.technicianIdentity ?? '';
        closeButton.dataset.description = antenna.latestIntervention?.description ?? '';

        return row;
    }));
}

export function renderPagination({ prev, next, info }, meta) {
    prev.disabled = meta.current_page <= 1;
    next.disabled = meta.current_page >= meta.last_page;
    info.textContent = `${meta.current_page} / ${meta.last_page}`;
}