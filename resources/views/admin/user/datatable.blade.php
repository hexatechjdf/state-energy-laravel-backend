<script>
    var user_table = null;
    var checkedStatuses = {};
    (function($) {
        "use strict";

        $(function() {
            if ($.fn.DataTable.isDataTable('#user-table')) {
                $('#user-table').DataTable().destroy();
            }

            user_table = $('#user-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: ({
                    url: "{{ route('admin.user.table-data')}}",
                    method: "POST",
                    data: function(d) {
                        d._token = '{{ csrf_token() }}'
                    },
                    error: function(request, status, error) {
                        console.log(request.responseText);
                    }
                }),
                "columns": [
                    {
                        data: "id",
                        name: "id"
                    },
                    {
                        data: "email",
                        name: "email"
                    },
                    {
                        data: "location_id",
                        name: "location_id"
                    },
                    {
                        data: "user_id",
                        name: "user_id"
                    },
                    {
                        data: "first_name",
                        name: "first_name"
                    },
                    {
                        data: "phone",
                        name: "phone"
                    },
                     {
                        data: "country",
                        name: "country"
                    },
                     {
                        data: "zip_code",
                        name: "zip_code"
                    },
                     {
                        data: "city",
                        name: "city"
                    },
                   
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }

                ],
                responsive: true,
                "bStateSave": true,
                "bAutoWidth": false,
                "ordering": false,
                "searching": true,
                "language": {
                    "decimal": "",
                    "emptyTable": "@lang('translation.no_data_found')",
                    "info": "@lang('translation.showing')" + " _START_ " + "@lang('translation.to')" + " _END_ " +
                        "@lang('translation.of')" +
                        " _TOTAL_ " + "@lang('translation.entries')",
                    "infoEmpty": "@lang('translation.showing_0_to_0_of_0_entries')",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "@lang('translation.show')" + " _MENU_ " + "@lang('translation.entries')",
                    "loadingRecords": "@lang('translation.loading')",
                    "processing": "@lang('translation.processing')",
                    "search": "@lang('translation.search')",
                    "zeroRecords": "@lang('translation.no_matching_records_found')",
                    "paginate": {
                        "first": "@lang('translation.first')",
                        "last": "@lang('translation.last')",
                        "previous": "<i class='ti-angle-left'></i>",
                        "next": "<i class='ti-angle-right'></i>"
                    }
                },
                createdRow: function(row, data, dataIndex) {
                    // Assign a unique ID to each row based on the device's ID
                    $(row).attr('id', 'user-' + data.id);
                },
                drawCallback: function(settings) {
                    $(".dataTables_paginate > .pagination").addClass("pagination-bordered");
                }
            });
        });

    })(jQuery);
</script>