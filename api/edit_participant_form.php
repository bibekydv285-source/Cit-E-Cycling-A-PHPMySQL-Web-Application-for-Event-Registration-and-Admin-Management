<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <h4 class="fw-bold mb-1">Edit Participant Scores</h4>
        <p class="text-muted mb-4">Only power output and distance can be updated.</p>

        <form action="/edit-participant" method="POST" novalidate id="editForm">
            <div class="mb-3">
                <label class="form-label fw-semibold">First Name</label>
                <input type="text" class="form-control bg-light" name="firstname" disabled value="<?= htmlspecialchars($participant['firstname']) ?>">
                <div class="form-text">Read-only field.</div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Surname</label>
                <input type="text" class="form-control bg-light" name="surname" disabled value="<?= htmlspecialchars($participant['surname']) ?>">
                <div class="form-text">Read-only field.</div>
            </div>
            <div class="mb-3">
                <label for="power_output" class="form-label fw-semibold">Power Output (watts) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="power_output" name="power_output" min="0" step="0.1"
                    value="<?= htmlspecialchars($participant['power_output']) ?>" required>
                <div class="invalid-feedback">Please enter a valid non-negative number.</div>
            </div>
            <div class="mb-4">
                <label for="distance_travelled" class="form-label fw-semibold">Distance Travelled (km) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="distance_travelled" name="distance_travelled" min="0" step="0.1"
                    value="<?= htmlspecialchars($participant['distance']) ?>" required>
                <div class="invalid-feedback">Please enter a valid non-negative number.</div>
            </div>
            <input type="hidden" name="id" value="<?= htmlspecialchars($participant['id']) ?>">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-warning fw-semibold">💾 Save Changes</button>
                <a href="/view-participants-edit-delete" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('editForm').addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
    }
    this.classList.add('was-validated');
});
</script>