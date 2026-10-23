import defaultOptions, { clearToken } from '../defaultOptions.js';

async function request(url, { method = 'GET', body } = {}) {
    const { headers } = defaultOptions();
    const response = await fetch(url, {
        method,
        headers: body ? { ...headers, 'Content-Type': 'application/json' } : headers,
        body: body && JSON.stringify(body),
    });

    if (response.status === 401) {
        clearToken();
    }

    const data = await response.json().catch(() => ({}));
    if (!response.ok) {
        throw { status: response.status, ...data };
    }

    return data;
}

export const fetchAntennas = (params) => request(`/api/antennas?${new URLSearchParams(params)}`);

export const createIntervention = (antennaId, body) =>
    request(`/api/antennas/${antennaId}/interventions`, { method: 'POST', body });

export const closeIntervention = (interventionId, body) =>
    request(`/api/interventions/${interventionId}`, { method: 'PATCH', body });
