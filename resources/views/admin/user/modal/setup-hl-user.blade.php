<!-- Setup Hpp Modal -->
<div class="modal" id="assignHLUser" tabindex="-1" aria-labelledby="assignHLUserLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="setup-hpp-form">
            @csrf
            <input type="hidden" name="location_id" id="location_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignHLUserLabel">Assign HL User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                     <div class="mb-3">
                        <label for="user_id" class="form-label">Select User</label>
                        <select class="form-select" id="user_id" name="user_id" required>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
