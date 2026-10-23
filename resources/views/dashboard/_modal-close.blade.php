<dialog id="close-intervention-modal" class="dashboard__dialog">
    <form id="close-intervention-form" class="dashboard__form">
        <h2 class="dashboard__form-title">Close intervention</h2>
        <div class="dashboard__error alert alert-danger" data-error hidden></div>
        <input type="hidden" name="intervention_id">
        <input type="text" name="technician_identity" class="form-control" placeholder="Technician identity" maxlength="255" required>
        <textarea name="description" class="form-control" placeholder="Description" maxlength="255" required></textarea>
        <label class="dashboard__label">End date (optional)
            <input type="datetime-local" name="end_date" class="form-control">
        </label>
        <div class="dashboard__actions">
            <button type="button" class="btn btn-outline-secondary" data-cancel>Cancel</button>
            <button type="submit" class="btn btn-danger">Close intervention</button>
        </div>
    </form>
</dialog>
