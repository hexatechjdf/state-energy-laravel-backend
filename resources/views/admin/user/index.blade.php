@extends('layouts.master-layouts')

@section('title')
    @lang('translation.Data_Tables')
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    {{-- @component('components.breadcrumb')
        @slot('li_1')
            Merchant
        @endslot
        @slot('title')
            List
        @endslot
    @endcomponent --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title d-flex justify-content-between align-items-center">
                        <span>Users</span>
                        {{-- <button class="btn btn-primary float-end" id="btn-add">Add</button> --}}
                    </h4>
                    <table id="user-table" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Email</th>
                                <th>location Id</th>
                                <th>Location Name</th>
                                <th>User Id</th>
                                <th>Phone</th>
                                <th>Country</th>
                                <th>Zip Code</th>
                                <th>City</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
    @include('admin.user.modal.setup-hl-user')

@endsection
@section('script')
    <!-- Required datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
    @include('admin.user.datatable')
    @include('admin.user.script')
@endsection
