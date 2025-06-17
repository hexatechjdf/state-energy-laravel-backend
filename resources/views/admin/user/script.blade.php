<script>
    $(document).ready(function() {
        // Open Edit Modal and Populate Data
        $(document).on('click', '.btn-add-hpp', function() {
            var locationId = $(this).data('location-id');
            var selectedUserId = $(this).data('user-id') || '';
            $('#loader').show();
            $.ajax({
                    url: `/api/v1/user/get-hl-user?location_id=${locationId}`,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var $userSelect = $('#user_id');
                            $userSelect.empty(); // clear previous options
                            $userSelect.append(`<option value="">Select a user</option>`); // default option
                            
                            $.each(response.data.users, function(index, user) {
                                var selected = (user.id === selectedUserId) ? 'selected' : '';
                                $userSelect.append(
                                  `<option value="${user.id}" ${selected}>${user.name}</option>`
                                ); });
                        } else {
                            alert('Failed to load users.');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert('Error fetching users.');
                    },
                    complete: function() {
                        // Show modal after users loaded
                        $('#loader').hide();
                        $('#assignHLUser').modal('show');
                    }
                });
        });

        $('#setup-hpp-form').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            var data = $(this).serialize();
            var url = '#';
            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                success: function(response) {
                    try {
                        $('#assignHLUser').modal('hide');
                        $form.removeClass('was-validated');
                        toastr.success('Saved');
                        $('#merchant-table').DataTable().ajax.reload(null, false);
                    } catch (error) {
                        toastr.error(error);
                    }
                    console.log('Data saved successfully:', response);
                },
                error: function(xhr, status, error) {
                    console.error('Error saving data:', error);
                }
            });
        });
    });
</script>
