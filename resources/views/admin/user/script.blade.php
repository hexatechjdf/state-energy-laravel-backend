<script>

    $(document).ready(function() {
        var editPhoneIntlInput = window.intlTelInput(document.querySelector("#edit_phone"), {
            separateDialCode: true,
            preferredCountries: ["pk", "us", "gb"],
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/23.0.10/js/utils.min.js"
        });
        // Open Edit Modal and Populate Data
        $(document).on('click', '.btn-edit-user', function() {
             $('#loader').show();
            var uuid = $(this).data('id');
            $('#edit_uuid').val(uuid);
            let selectedUserId = $(this).data('user-id');
            let locationId = $(this).data('location-id');
            $('#edit-user-form')[0].reset(); // clear form
            editPhoneIntlInput.setNumber(''); // clear phone input intl-tel-input too
            var userShowRoute = "{{ route('admin.users.show', ['id' => 'USER_ID_PLACEHOLDER']) }}";
            let url = userShowRoute.replace('USER_ID_PLACEHOLDER', uuid) + `?location_id=${locationId}`;
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                     $('#loader').hide();
                    if (response.success) {
                        var $userSelect = $('#edit_user_id');
                        $userSelect.empty(); // clear previous options
                        $userSelect.append(`<option value="">Select a user</option>`); // default option
                        
                        $.each(response.data.crmUser.users, function(index, user) {
                            var selected = (user.id === selectedUserId) ? 'selected' : '';
                            $userSelect.append(
                              `<option value="${user.id}" ${selected}>${user.name}</option>`
                            ); });

                        let user = response.data.user;
                        let dialCode = user.dial_code.startsWith('+') ? user.dial_code : `+${user.dial_code}`;
                        let fullPhoneNumber = `${dialCode}${user.phone}`;
                        editPhoneIntlInput.setNumber(fullPhoneNumber);
                        $('#edit_first_name').val(user.first_name);
                        $('#edit_last_name').val(user.last_name);
                        $('#edit_email').val(user.email);
                        $('#edit_country').val(user.country);
                        $('#edit_city').val(user.city);
                        $('#edit_zip_code').val(user.zip_code);
                        $('#editUserModal').modal('show');
                    } else {
                         $('#loader').hide();
                        toastr.error('User not found');
                    }
                },
                error: function() {
                     $('#loader').hide();
                    toastr.error('Failed to fetch user details.');
                }
            });
        });
        $('#edit-user-form').on('submit', function(e) {
            e.preventDefault();
            var url = "{{ route('admin.update.profile')}}";
            var dialCode = editPhoneIntlInput.getSelectedCountryData().dialCode;
            var form = $(this);
            form.find('input[name="dial_code"]').remove();
            form.append('<input type="hidden" name="dial_code" value="' + dialCode + '">');
            var data = form.serialize();
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        toastr.success('User updated successfully!');
                        $('#editUserModal').modal('hide');
                        $('#user-table').DataTable().ajax.reload(null, false);
                        // reload your table/list if needed here
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Failed to update user.');
                }
            });
        });

        $('#assign-hl-form').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            var data = $(this).serialize();
            var uuid = $('#uuid').val()
            var urlTemplate = "{{ route('admin.update.profile', ['id' => '__uuid__']) }}";
            var url = urlTemplate.replace('__uuid__', uuid);
            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                success: function(response) {
                    try {
                        $('#assignHLUser').modal('hide');
                        $form.removeClass('was-validated');
                        toastr.success('CRM User Assigned.');
                        $('#user-table').DataTable().ajax.reload(null, false);
                    } catch (error) {
                        toastr.error(error);
                    }
                    console.log('Data saved successfully:', response);
                },
                error: function(xhr, status, error) {
                    console.error('Error saving data:', error);
                    toastr.error(xhr.responseJSON.message);

                }
            });
        });

        $(document).on('click', '#btn-add', function() {
            $('#loader').show();
            var locationId = $(this).data('location-id');
            var selectedUserId = '';
            $.ajax({
                    url: `/api/v1/user/get-hl-user?location_id=${locationId}`,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var $userSelect = $('#add_user_id');
                            $userSelect.empty(); // clear previous options
                            $userSelect.append(`<option value="">Select a user</option>`); // default option
                            
                            $.each(response.data.users, function(index, user) {
                                var selected = (user.id === selectedUserId) ? 'selected' : '';
                                $userSelect.append(
                                  `<option value="${user.id}" ${selected}>${user.name}</option>`
                                ); });
                        } else {
                            toastr.error('Failed to load Users');
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message);

                    },
                    complete: function() {
                        // Show modal after users loaded
                        $('#loader').hide();
                        $('#createUserModal').modal('show');

                    }
                });
        });
        
        let iti;
        document.getElementById('createUserModal').addEventListener('shown.bs.modal', function () {
        const input = document.querySelector("#phone");
        if (iti) {
            iti.destroy();
        }
        iti = window.intlTelInput(input, {
            separateDialCode: true,
            dropdownContainer: document.getElementById('createUserModal'),
            preferredCountries: ["pk", "us", "gb", "ae", "au"],
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/23.0.10/js/utils.min.js"
        });
        });
        $('#create-user-form').on('submit', function(e) {
            e.preventDefault();
            $('#loader').show();
            var form = $(this);
            var url = "{{ route('admin.users.store') }}";
            var dialCode = iti.getSelectedCountryData().dialCode;
            form.find('input[name="dial_code"]').remove();
            form.append('<input type="hidden" name="dial_code" value="' + dialCode + '">');
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
                    $('#createUserModal').modal('hide');
                    form[0].reset();
                    $('#loader').hide();
                    toastr.success('User Added Successfully.');
                    $('#user-table').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    $('#loader').hide();
                    toastr.error(xhr.responseJSON.message);
                    console.log(xhr.responseJSON.message);
                }
            });
        });
        $(document).on('click', '.btn-delete-user', function() {
    let userId = $(this).data('id');

    Swal.fire({
        title: 'Are you sure?',
        text: "This user record will be permanently deleted.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/users/${userId}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Deleted!',
                            'User has been deleted.',
                            'success'
                        );
                    $('#user-table').DataTable().ajax.reload(null, false);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Failed to delete user.');
                }
            });
        }
    });
});

    });
</script>
