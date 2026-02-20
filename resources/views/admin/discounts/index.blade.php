@extends('layouts.admin')
@section('title', 'Admin | Discounts')

@section('content')
    <div class="py-2 my-3 bg-white border row align-items-center">
        <div class="col-sm-6">
            <span class="my-auto h6 page-headder">@yield('page-headder')</span>
            <ol class="bg-white breadcrumb">
                <li class="breadcrumb-item"> <a href="{{ route('admin.dashboard') }}" class="text-primary"><i class="fa fa-home"></i> </a></li>
                <li class="px-2 breadcrumb-item text-dark"><a href="{{ route('admin.discounts.index') }}"> Discounts</a></li>
            </ol>
        </div>
        <div class="col-md-6">
            <ol class="float-end button">
                <a href="{{ route('admin.discounts.create') }}" class="btn btn-success action-btn" data-action="add" title="Add" id="add">
                    <span class="default-text"><i class="fa fa-plus"></i></span>
                    <span class="spinner-border spinner-border-sm d-none"></span><span class="">Add New Discounts</span>
                </a>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="p-0 col-12">
            <div class="card">
                <div class="py-3 card-header justify-content-between">
                    <h4 class="float-start pt-2 card-title">All Discounts</h4>
                </div>

                <div class="p-0 card-body table-responsive">
                    @include('admin.discounts.partials.discounts_table')
                </div>
            </div>
        </div>
    </div>

@endsection
