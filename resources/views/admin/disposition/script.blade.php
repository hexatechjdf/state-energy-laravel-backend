<script>
    $(document).ready(function() {
        $(document).on('click', '.btn-edit-disposition', function() {
            var uuid = $(this).data('id');
            var name = $(this).data('name');
            $('#edit_uuid').val(uuid);
            $('#edit-disposition-form')[0].reset(); // clear form
            $('#edit_name').val(name);
            $('#editDispositionModal').modal('show');
        });
        $('#edit-disposition-form').on('submit', function(e) {
            e.preventDefault();
            var url = "{{ route('admin.disposition.update') }}";
            var form = $(this);
            var data = form.serialize();
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        toastr.success('Disposition updated successfully!');
                        $('#editDispositionModal').modal('hide');
                        $('#disposition-table').DataTable().ajax.reload(null, false);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Failed to update disposition.');
                }
            });
        });

        $(document).on('click', '#btn-add', function() {
            $('#createDispositionModal').modal('show');
        });

        $('#create-disposition-form').on('submit', function(e) {
            e.preventDefault();
            $('#loader').show();
            var form = $(this);
            var url = "{{ route('admin.disposition.store') }}";
            var data = form.serialize();
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log(response.message);
                    $('#createDispositionModal').modal('hide');
                    form[0].reset();
                    $('#loader').hide();
                    toastr.success('Disposition Added Successfully.');
                    $('#disposition-table').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    $('#loader').hide();
                    toastr.error(xhr.responseJSON.message);
                    console.log(xhr.responseJSON.message);
                }
            });
        });
        $(document).on('click', '.btn-delete-disposition', function() {
            let dispositionId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "This disposition record will be permanently deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/disposition/delete/${dispositionId}`,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Deleted!',
                                    'Disposition has been deleted.',
                                    'success'
                                );
                                $('#disposition-table').DataTable().ajax.reload(
                                    null,
                                    false);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            toastr.error('Failed to delete disposition.');
                        }
                    });
                }
            });
        });

    });
</script>
