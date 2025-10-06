<!-- Edit User Modal -->
<div class="modal fade" id="editLenderModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="edit-lender-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="edit_uuid" name="uuid" />

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit Lender</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_name" class="form-label">Name</label>
                            <input type="url" class="form-control" id="edit_url" name="url" required>
                        </div>


                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Logo</label>
                            <div class="card p-2">
                                <img id="logo_preview" src="" alt="Current Logo"
                                    class="img-fluid rounded mb-2" style="height: 180px; object-fit: cover;">
                                <input type="file" class="form-control" name="logo"
                                    onchange="previewLogo(this)">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer pt-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Lender</button>
                </div>
            </div>
        </form>
    </div>
</div>
