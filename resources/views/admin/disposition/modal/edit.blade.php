<!-- Edit User Modal -->
<div class="modal fade" id="editDispositionModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="edit-disposition-form">
            @csrf
            <input type="hidden" id="edit_uuid" name="uuid" />

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit Disposition</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                      
                    </div>
                </div>

                <div class="modal-footer pt-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Disposition</button>
                </div>
            </div>
        </form>
    </div>
</div>
