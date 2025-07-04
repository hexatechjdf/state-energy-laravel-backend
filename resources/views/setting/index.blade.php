@extends('layouts.master-layouts')

@section('title')
    @lang('translation.Setting')
@endsection
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-results__options {
            max-height: 250px !important;
            overflow-y: auto !important;
        }

        .select2-container .select2-selection--single {
            display: flex !important;
        }
    </style>
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
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Webhook URL Setting for Order Payload</h4>
                    <form class="needs-validation" novalidate id="webhook-setting-form" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="order_webhook_url" class="form-label">
                                        Webhook URL
                                        <small class="text-muted d-block">This is the endpoint where order payloads will be
                                            sent automatically when an order is placed.</small>
                                    </label>
                                    <input type="url" class="form-control" id="order_webhook_url"
                                        name="setting[order_webhook_url]"
                                        placeholder="https://your-webhook-endpoint.com/receive-order"
                                        value="{{ $settings['order_webhook_url'] ?? '' }}" required>
                                    <div class="valid-feedback">
                                        Looks good!
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div> <!-- end col -->
 <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Webhook URL Disposition Payload</h4>
                    <form class="needs-validation" novalidate id="disposition-setting-form" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="disposition_webhook_url" class="form-label">
                                        Webhook URL
                                        <small class="text-muted d-block">This is the endpoint where disposition payload will be
                                            sent.</small>
                                    </label>
                                    <input type="url" class="form-control" id="disposition_webhook_url"
                                        name="setting[disposition_webhook_url]"
                                        placeholder="https://your-webhook-endpoint.com/receive-order"
                                        value="{{ $settings['disposition_webhook_url'] ?? '' }}" required>
                                    <div class="valid-feedback">
                                        Looks good!
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div> <!-- end col -->

        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Template Setting for Proposal Sending</h4>
                    <form class="needs-validation" novalidate id="template-selection-form">
                        @csrf
                        <input type="hidden" id="proposal_template_name" name="setting[proposal_template_name]"
                            value="">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="proposal_template_id" class="form-label">
                                        Select Email Template for Proposal
                                    </label>
                                    <select class="form-select" id="proposal_template_id"
                                        name="setting[proposal_template_id]" style="width: 100%;">
                                        @if (!empty($settings['proposal_template_id']) && !empty($settings['proposal_template_name']))
                                            <option value="{{ $settings['proposal_template_id'] }}" selected>
                                                {{ $settings['proposal_template_name'] }}
                                            </option>
                                        @endif
                                    </select>
                                    <div class="valid-feedback">
                                        Looks good!
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div> <!-- end col -->



        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">CRM Setup</h4>
                    <code class="fw-bold fs-5">Private Integration Token</code>
                    <p class="card-title-desc">
                        To generate a Private Integration Token, go to the sub-account, navigate to <code
                            class="fw-bold fs-5">Settings </code>(bottom of the
                        sidebar), and select <code class="fw-bold fs-5"> Private Integration Token .</code> Click <code
                            class="fw-bold fs-5">Create New Integration, </code>enter a name and
                        description, then proceed. On the next page, set the scopes to <code class="fw-bold fs-5">Edit Conversation Messages View Calendars Edit Calendars View Calendar Events Edit Calendar Events View Users Edit Conversations View Contacts View Email Templates Edit Contacts.</code> Save the token, copy it, and paste it
                        here along with the <code class="fw-bold fs-5"> Location ID </code>used to
                        generate it.</p>
                    <form class="needs-validation" novalidate id="onboarding-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="location_id" class="form-label">Location Id</label>
                                    <input type="text" class="form-control" id="location_id"
                                        placeholder="Enter location id" name="setting[location_id]"
                                        value="{{ $settings['location_id'] ?? '' }}" required>
                                    <div class="valid-feedback">
                                        Looks good!
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="private_integration_token" class="form-label">
                                        Private Integration Token
                                        @if (!empty($settings['private_integration_token']))
                                            <small class="text-muted">(existing token:
                                                {{ substr($settings['private_integration_token'], -7) }})</small>
                                        @endif
                                    </label>
                                    <input type="text" class="form-control" id="private_integration_token"
                                        placeholder="{{ empty($settings['private_integration_token']) ? 'Enter private integration token' : 'Leave empty to use existing token' }}"
                                        name="setting[private_integration_token]" value="">
                                    <div class="valid-feedback">
                                        Looks good!
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div> <!-- end col -->
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Update Profile</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <form class="needs-validation" novalidate id="update-admin-profile">
                                @csrf

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="text" class="form-control" id="email"
                                                placeholder="Enter Email" name="email"
                                                value="{{ Auth::user()->email }}" required>
                                            <div class="valid-feedback">
                                                Looks good!
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="text" class="form-control" id="password"
                                                placeholder="Leave empty if you use want to use the old password."
                                                name="password" value="">
                                            <div class="valid-feedback">
                                                Looks good!
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button class="btn btn-primary" type="submit">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div> <!-- end col -->
    </div>


    <!-- end row -->
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/parsleyjs/parsleyjs.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/form-validation.init.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/form-editor.init.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#onboarding-form,#template-selection-form,#webhook-setting-form,#disposition-setting-form').on('submit',
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
                    url: "{{ route('admin.update.profile', Auth::user()?->id) }}",
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    data: formData, // Send serialized form data
                    success: function(result) {
                        if (result.success === true) {
                            $form.removeClass('was-validated');
                            toastr.success(result.message ?? 'Profile Upated Successfully.');
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
            $('#proposal_template_id').select2({
                theme: 'bootstrap-5', // optional if you use Bootstrap styling
                placeholder: 'Select Email Template',
                allowClear: true,
                ajax: {
                    url: '{{ route('admin.email-templates.list') }}', // your Laravel route
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search query
                            page: params.page || 1 // page number for pagination
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        return {
                            results: $.map(data.data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                };
                            }),
                            pagination: {
                                more: (params.page * data.per_page) < data.total
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0
            });
            $('#proposal_template_id').on('select2:select', function(e) {
                var selected = e.params.data;
                $('#proposal_template_name').val(selected.text);
            });

            // Optional: Clear name if user clears selection
            $('#proposal_template_id').on('select2:clear', function(e) {
                $('#proposal_template_name').val('');
            });
        });
    </script>
@endsection
