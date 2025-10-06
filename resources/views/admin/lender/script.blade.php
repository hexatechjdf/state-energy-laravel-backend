<script>
    window.previewAddLogo = function(input) {
        $('#add_logo_preview').show();
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => $('#add_logo_preview').attr('src', e.target.result);
            reader.readAsDataURL(input.files[0]);
        }
    }
    window.previewLogo = function(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => $('#logo_preview').attr('src', e.target.result);
            reader.readAsDataURL(input.files[0]);
        }
    }
    $(document).ready(function() {
        $(document).on('click', '.btn-edit-lender', function() {
            var uuid = $(this).data('id');
            var url = $(this).data('url');
            var logo = $(this).data('logo');
            $('#edit_uuid').val(uuid);
            $('#edit-lender-form')[0].reset(); // clear form
            $('#edit_url').val(url);
            $('#logo_preview').attr('src', logo);
            $('#editLenderModal').modal('show');
        });
        $('#edit-lender-form').on('submit', function(e) {
            e.preventDefault();
            var url = "{{ route('admin.lender.update') }}";
            var form = $(this);
            const formData = new FormData(this);
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        toastr.success('Lender updated successfully!');
                        $('#editLenderModal').modal('hide');
                        $('#lender-table').DataTable().ajax.reload(null, false);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Failed to update lender.');
                }
            });
        });

        $(document).on('click', '#btn-add', function() {
            $('#add_logo_preview').hide();
            $('#createLenderModal').modal('show');
        });

        $('#create-lender-form').on('submit', function(e) {
            e.preventDefault();
            $('#loader').show();
            var form = $(this);
            var url = "{{ route('admin.lender.store') }}";
            const formData = new FormData(this);
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log(response.message);
                    $('#createLenderModal').modal('hide');
                    form[0].reset();
                    $('#add_logo_preview').attr('src', '');
                    $('#loader').hide();
                    toastr.success('lender Added Successfully.');
                    $('#lender-table').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    $('#loader').hide();
                    toastr.error(xhr.responseJSON.message);
                    console.log(xhr.responseJSON.message);
                }
            });
        });
        $(document).on('click', '.btn-delete-lender', function() {
            let lenderId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "This lender record will be permanently deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/lender/delete/${lenderId}`,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Deleted!',
                                    'Lender has been deleted.',
                                    'success'
                                );
                                $('#lender-table').DataTable().ajax.reload(
                                    null,
                                    false);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            toastr.error('Failed to delete lender.');
                        }
                    });
                }
            });
        });

    });
</script>
