@extends('layouts.master-layouts')

@section('title')
    @lang('translation.Lender')
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .adders-column {
            max-width: 250px;
            white-space: normal !important;
            word-wrap: break-word;
        }

        .table-image {
            width: 100%;
            height: 100px;
            object-fit: contain;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title d-flex justify-content-between align-items-center">
                        <span>@lang('translation.Lender')</span>
                        <button class="btn btn-primary float-end" id="btn-add">Add</button>
                    </h4>
                    <table id="lender-table" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Url</th>
                                <th>Logo</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('admin.lender.modal.add')
    @include('admin.lender.modal.edit')

@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @include('admin.lender.datatable')
    @include('admin.lender.script')

@endsection
