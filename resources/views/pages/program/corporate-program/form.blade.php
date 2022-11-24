@extends('layout.main')

@section('title', 'Partner Program - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('program/client') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Partner Program
        </a>
    </div>


    <div class="row">
        <div class="col-md-4">
            <div class="card rounded mb-3">
                <div class="card-body text-center">
                    <h4>Partner Name</h4>
                    <h6>Program Name</h6>
                    <div class="mt-3 d-flex justify-content-center">
                        <button type="button" class="btn btn-sm btn-outline-danger rounded mx-1">
                            <i class="bi bi-trash2"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
            @include('pages.program.corporate-program.detail.corporate')
            @include('pages.program.corporate-program.detail.speaker')
        </div>

        <div class="col-md-8">
            <div class="card rounded">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-tags me-2"></i>
                            Partner Program Detail
                        </h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label for="">
                                Is Corporate Scheme?
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-3">
                                    <select name="" id="" class="select w-100">
                                        <option data-placeholder="true"></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label for="">
                                Date
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <small>First Discuss</small>
                                    <input type="date" name="" id=""
                                        class="form-control form-control-sm rounded">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label for="">
                                Participants
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <small>Amount</small>
                                    <input type="number" name="" id=""
                                        class="form-control form-control-sm rounded">
                                </div>
                                <div class="col-md-6">
                                    <small>Total Price</small>
                                    <input type="number" name="" id=""
                                        class="form-control form-control-sm rounded">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="">
                                Program Status
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <small>Status</small>
                                    <select name="" id="" class="select w-100"></select>
                                </div>
                                <div class="col-md-6">
                                    <small>Date</small>
                                    <input type="date" name="" id=""
                                        class="form-control form-control-sm rounded">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="">
                                Notes
                            </label>
                        </div>
                        <div class="col-md-9">
                            <textarea name="" id="" class="w-100"></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="">Attachments</label>
                        </div>
                        <div class="col-md-9">
                            <div class="card">
                                <div class="card-header">
                                    Files
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="">
                                                Attachment 1
                                            </label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" name="" id=""
                                                class="form-control form-control-sm mb-2">
                                            <input type="file" name="" id=""
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="">
                                                Attachment 2
                                            </label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" name="" id=""
                                                class="form-control form-control-sm mb-2">
                                            <input type="file" name="" id=""
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="">
                                                Attachment 3
                                            </label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" name="" id=""
                                                class="form-control form-control-sm mb-2">
                                            <input type="file" name="" id=""
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label for="">
                                PIC
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <select name="" id="" class="select w-100">
                                        <option data-placeholder="true"></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="mt-3 text-end">
                        <button class="btn btn-sm btn-primary rounded">
                            <i class="bi bi-save2 me-2"></i> Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {

        })
    </script>
@endsection
