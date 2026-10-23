An API docs OpenAPI is generated using Scramble at http://localhost:8080/docs/api

export TOKEN=change-me
export BASE=http://localhost:8080/api

## Authentication

Write routes use `Route::middleware('api.token')`.
Change `API_TOKEN` in `.env`.
Send the token configured in `API_TOKEN`:

```bash
curl -H "Authorization: Bearer change-me" -H "Accept: application/json" ...
```

# 1. List antennas (filters: city (partial, case-insensitive), status=UP|DOWN, page, per_page<=100)
curl -s "$BASE/antennas?city=Paris&status=DOWN&per_page=20&page=1" \
  -H "Authorization: Bearer $TOKEN" -H "Accept: application/json"

# 2. Create an intervention on antenna 1 -> 201 {"intervention_id": N}; antenna becomes DOWN
curl -s -X POST "$BASE/antennas/1/interventions" \
  -H "Authorization: Bearer $TOKEN" -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"description":"Replace faulty module","technician_identity":"John Doe","priority":"HIGH"}'

# 3. Close intervention N -> 200; antenna becomes UP (end_date is optional)
curl -s -X PATCH "$BASE/interventions/1" \
  -H "Authorization: Bearer $TOKEN" -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"description":"Module replaced","technician_identity":"John Doe","end_date":"2026-10-23T10:00:00+00:00"}'