<form id="search-form" class="dashboard__search">
    <input type="search" name="city" class="form-control" placeholder="City">
    <select name="status" class="form-select">
        <option value="">All statuses</option>
        <option value="UP">UP</option>
        <option value="DOWN">DOWN</option>
    </select>
    <button type="submit" class="btn btn-secondary">Search</button>
</form>

<table class="dashboard__table">
    <thead>
        <tr>
            <th>Antenna</th>
            <th>City</th>
            <th>Status</th>
            <th>Latest intervention</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="antenna-rows"></tbody>
</table>

<nav class="dashboard__pagination">
    <button type="button" id="prev" class="btn btn-outline-secondary btn-sm">Previous</button>
    <span id="page-info"></span>
    <button type="button" id="next" class="btn btn-outline-secondary btn-sm">Next</button>
</nav>

<template id="antenna-row">
    <tr>
        <td data-cell="id"></td>
        <td data-cell="city"></td>
        <td data-cell="status"></td>
        <td data-cell="latest-intervention" style="white-space: pre"></td>
        <td>
            <button type="button" class="btn btn-primary btn-sm" data-action="create">Create intervention</button>
            <button type="button" class="btn btn-danger btn-sm" data-action="close">Close intervention</button>
        </td>
    </tr>
</template>
