<!-- Create User Modal -->
<div class="modal fade" id="createLenderModal" tabindex="-1" aria-labelledby="createLenderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="create-lender-form" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createLenderModalLabel">Create New Lender</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">    
                            <label for="name" class="form-label">URL</label>
                            <input type="url" class="form-control" id="url" name="url" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Logo</label>
                            <div class="card p-2">
                                <input type="file" class="form-control" name="logo"
                                    onchange="previewAddLogo(this)">
                                <img id="add_logo_preview" src="" alt="Current Logo"
                                    class="img-fluid rounded mb-2" style="height: 180px; object-fit: cover;">

                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer pt-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Lender</button>
                </div>
            </div>
        </form>
    </div>
</div>
