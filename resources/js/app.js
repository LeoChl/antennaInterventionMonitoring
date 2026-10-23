import { fetchAntennas, createIntervention, closeIntervention } from './api/antennas.js';
import { renderAntennas, renderPagination } from './ui/antennaTable.js';
import { setupModal } from './ui/modal.js';

const $ = (selector) => document.querySelector(selector);
const state = { page: 1 };

async function load() {
    const filters = Object.fromEntries([...new FormData($('#search-form'))].filter(([, v]) => v !== ''));
    const { data, meta } = await fetchAntennas({ ...filters, page: state.page });
    renderAntennas($('#antenna-rows'), $('#antenna-row'), data);
    renderPagination({ prev: $('#prev'), next: $('#next'), info: $('#page-info') }, meta);
}

const openCreate = setupModal($('#create-intervention-modal'), async ({ antenna_id, ...body }) => {
    await createIntervention(antenna_id, body);
    await load();
});
const openClose = setupModal($('#close-intervention-modal'), async ({ intervention_id, ...body }) => {
    await closeIntervention(intervention_id, body);
    await load();
});

$('#antenna-rows').addEventListener('click', ({ target }) => {
    const { action, antennaId, interventionId, technicianIdentity, description } = target.closest('[data-action]')?.dataset ?? {};
    if (action === 'create') openCreate({ antenna_id: antennaId });
    if (action === 'close') openClose({ intervention_id: interventionId, technician_identity: technicianIdentity, description });
});

$('#search-form').addEventListener('submit', (e) => { e.preventDefault(); state.page = 1; load(); });
$('#prev').addEventListener('click', () => { state.page--; load(); });
$('#next').addEventListener('click', () => { state.page++; load(); });

load();