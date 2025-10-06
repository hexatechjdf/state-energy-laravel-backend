<!-- Create User Modal -->
<div class="modal fade" id="createDispositionModal" tabindex="-1" aria-labelledby="createDispositionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="create-disposition-form">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createDispositionModalLabel">Create New Disposition</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                          <div class="col-md-3"></div>
                        <div class="col-md-4">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer pt-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Disposition</button>
                </div>
            </div>
        </form>
    </div>
</div>
