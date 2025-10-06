<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="edit-category-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="category_id" id="edit_category_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- Name -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Name</label>
                            <input type="text" class="form-control" name="name" id="edit_name"
                                placeholder="Enter category name" readonly>
                        </div>

                        <!-- Thumbnail -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Thumbnail</label>
                            <div class="card p-2">
                                <img id="edit_thumbnail_preview" src="" alt="Current Thumbnail"
                                    class="img-fluid rounded mb-2" style="height: 180px; object-fit: cover;">
                                <input type="file" class="form-control" name="thumbnail"
                                    onchange="previewThumbnail(this)">
                            </div>
                        </div>

                        <!-- Detail Photo -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Detail Photo</label>
                            <div class="card p-2">
                                <img id="edit_detail_photo_preview" src="" alt="Current Detail Photo"
                                    class="img-fluid rounded mb-2" style="height: 180px; object-fit: cover;">
                                <input type="file" class="form-control" name="detail_photo"
                                    onchange="previewDetailPhoto(this)">
                            </div>
                        </div>
                        <div class="col-md-12 mb-3 d-none d-flex" style="justify-content: space-around;"
                            id="cat-price-wrapper">
                            <div class="col-md-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">MSRP</span>
                                    </div>
                                    <input type="number" class="form-control" name="cat_base_price" id="cat-base-price"
                                        min="1">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Min</span>
                                    </div>
                                    <input type="number" class="form-control" name="cat_min_price" id="cat-min-price"
                                        min="1">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Max</span>
                                    </div>
                                    <input type="number" class="form-control" name="cat_max_price" id="cat-max-price"
                                        min="1">
                                </div>
                            </div>
                        </div>

                        <!-- Adders -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Adders</label>
                            <div id="edit_adders_wrapper"></div>

                            <button type="button" class="btn btn-sm btn-primary mt-2" id="btn-add-adder">
                                + Add Adder
                            </button>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Configuration Fields</label>
                            <div id="configuration_fields_container"></div>
                            {{-- <button type="button" class="btn btn-sm btn-primary mt-2" onclick="addConfigField()">+ Add Field</button> --}}
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </div>
        </form>
    </div>
</div>
