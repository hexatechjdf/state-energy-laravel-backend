@extends('layouts.master-layouts')

@section('title')
    @lang('translation.Setting')
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Forms
        @endslot
        @slot('title')
            Setting
        @endslot
    @endcomponent

    <div class="row">
    </div> <!-- end col -->
    </div>


    <!-- end row -->
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/parsleyjs/parsleyjs.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/form-validation.init.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/form-editor.init.js') }}"></script>
    <script>
        $("body").on('click', '#auto-login-url', function(e) {
            e.preventDefault();
            let msg = $(this).data('message');
            var url = $(this).closest('.copy-container').find('.auto-login').val();
            if (url) {
                navigator.clipboard.writeText(url).then(function() {
                    toastr.success(msg, {
                        timeOut: 10000
                    });
                }, function() {
                    toastr.error("Error while Copy", {
                        timeOut: 10000
                    });
                });
            } else {
                toastr.error("No data found to copy", {
                    timeOut: 10000
                });
            }
        });

        $(document).ready(function() {
            $('#oauth-information,#hpp-form,#location-page-view,#header-color,#onboarding-form').on('submit',
                function(e) {
                    e.preventDefault();
                    var $form = $(this);
                    var data = $(this).serialize();

                    var url = '{{ route('admin.setting.save') }}';
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: data,
                        success: function(response) {
                            try {
                                $form.removeClass('was-validated');
                                toastr.success('Saved');
                                if ($form.is('#header-color')) {
                                    var color = $('#color-input').val();
                                    $('#page-topbar').css('background-color', color)
                                }
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
            $('#update-admin-profile').on('submit', function(e) {
                e.preventDefault();
                const $form = $(this);
                var formData = $(this).serialize();
                var token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.update.profile') }}",
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    data: formData, // Send serialized form data
                    success: function(result) {
                        if (result.status === 'Success') {
                            $form.removeClass('was-validated');
                            toastr.success(result.message);
                        } else {
                            toastr.error('Error: ' + result.message);
                            console.error('Error:', result.message);
                        }
                    },
                    error: function(xhr) {
                        var error = JSON.parse(xhr.responseText);
                        toastr.error('Error: ' + error.message);
                        console.error('Error:', error.message);
                    }
                });
            });

        });
    </script>
@endsection
