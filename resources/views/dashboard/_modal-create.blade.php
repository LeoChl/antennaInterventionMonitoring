<dialog id="create-intervention-modal" class="dashboard__dialog">
    <form id="create-intervention-form" class="dashboard__form">
        <h2 class="dashboard__form-title">Create intervention</h2>
        <div class="dashboard__error alert alert-danger" data-error hidden></div>
        <input type="hidden" name="antenna_id">
        <textarea name="description" class="form-control" placeholder="Description" maxlength="255" required></textarea>
        <input type="text" name="technician_identity" class="form-control" placeholder="Technician identity" maxlength="255" required>
        <select name="priority" class="form-select" required>
            <option value="LOW">LOW</option>
            <option value="MEDIUM" selected>MEDIUM</option>
            <option value="HIGH">HIGH</option>
        </select>
        <div class="dashboard__actions">
            <button type="button" class="btn btn-outline-secondary" data-cancel>Cancel</button>
            <button type="submit" class="btn btn-primary">Create</button>
        </div>
    </form>
</dialog>
