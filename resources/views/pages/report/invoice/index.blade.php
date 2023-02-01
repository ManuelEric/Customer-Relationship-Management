@extends('layout.main')

@section('title', 'Finance Report - Bigdata Platform')

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="p-0 m-0">Period</h6>
                </div>
                <div class="card-body">
                    <form action="">
                        <div class="mb-3">
                            <label>Start Date</label>
                            <input type="date" name="start_date" id="" class="form-control form-control-sm rounded">
                        </div>
                        <div class="mb-3">
                            <label>End Date</label>
                            <input type="date" name="end_date" id="" class="form-control form-control-sm rounded">
                        </div>
                        <div class="text-center">
                            <button class="btn btn-sm btn-outline-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="p-0 m-0">Detail</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <strong>Total Invoice ({{count($invoices)}})</strong>
                        <div class="text-end">
                           Rp. {{ number_format($totalInvoice, '2', ',', '.') }}
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <strong>Total Receipt ({{count($receipts)}})</strong>
                        <div class="text-end">
                            Rp. 123.000.000
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Invoice List</h6>
                    <div class="">
                        <button class="btn btn-sm btn-outline-info">
                            <i class="bi bi-file-earmark-excel me-1"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100" id="volunteerTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoice ID</th>
                                    <th>Client/Partner/School Name</th>
                                    <th>Program Name</th>
                                    <th>Method</th>
                                    <th>Installment</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($invoices as $invoice)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ isset($invoice->inv_id) ? $invoice->inv_id : $invoice->invb2b_id}}</td>
                                        
                                        {{-- Client Name --}}
                                        @if(isset($invoice->clientprog_id))
                                            <td>{{ $invoice->clientprog->client->first_name }} {{ $invoice->clientprog->client->last_name }}</td>
                                        @elseif(isset($invoice->schprog_id))
                                            <td>{{ $invoice->sch_prog->school->sch_name }}</td>
                                        @elseif(isset($invoice->partnerprog_id))
                                            <td>{{ $invoice->partner_prog->corp->corp_name }}</td>
                                        @endif

                                        {{-- Program Name --}}
                                        @if(isset($invoice->clientprog_id))
                                            <td>{{ $invoice->clientprog->program->sub_prog ? $invoice->clientprog->program->sub_prog->sub_prog_name.' - ':''}}{{ $invoice->clientprog->program->prog_program }}</td>
                                        @elseif(isset($invoice->schprog_id))
                                            <td>{{ $invoice->sch_prog->program->sub_prog ? $invoice->sch_prog->program->sub_prog->sub_prog_name.' - ':''}}{{ $invoice->sch_prog->program->prog_program }}</td>
                                        @elseif(isset($invoice->partnerprog_id))
                                            <td>{{ $invoice->partner_prog->program->sub_prog ? $invoice->partner_prog->program->sub_prog->sub_prog_name.' - ':''}}{{ $invoice->partner_prog->program->prog_program }}</td>
                                        @endif 

                                        {{-- Method --}}
                                        <td>{{ isset($invoice->inv_id) ? $invoice->inv_paymentmethod : $invoice->invb2b_pm }}</td>
                                        
                                        {{-- Installment --}}
                                        <td>Installment</td>
                                        
                                        {{-- Due date --}}
                                        <td>{{ isset($invoice->inv_id) ? $invoice->inv_duedate : $invoice->invb2b_duedate }}</td>

                                        {{-- Amount --}}
                                        <td>{{ ($invoice->currency == 'idr') ? $invoice->invoiceTotalpriceIdr : $invoice->invoiceTotalprice }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Not yet invoice</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-light text-white">
                                <tr>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Receipt List</h6>
                    <div class="">
                        <button class="btn btn-sm btn-outline-info">
                            <i class="bi bi-file-earmark-excel me-1"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100" id="volunteerTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Receipt ID</th>
                                    <th>Client/Partner/School Name</th>
                                    <th>Program Name</th>
                                    <th>Method</th>
                                    <th>Installment</th>
                                    <th>Paid Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($receipts as $receipt)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $receipt->receipt_id }}</td>
                                        
                                        {{-- Client Name --}}
                                        @if(isset($receipt->inv_id))
                                            <td>{{ $receipt->invoiceProgram->clientprog->client->first_name }} {{ $receipt->invoiceProgram->clientprog->client->last_name }}</td>
                                        @elseif(isset($receipt->invb2b_id))
                                            @if(isset($receipt->invoiceB2b->schprog_id))
                                                <td>{{ $receipt->invoiceB2b->sch_prog->school->sch_name }}</td>
                                            @elseif(isset($receipt->invoiceB2b->partnerprog_id))
                                                <td>{{ $receipt->invoiceB2b->partner_prog->corp->corp_name }}</td>
                                            @endif
                                        @endif

                                        {{-- Program Name --}}
                                        @if(isset($receipt->inv_id))
                                            <td>{{ $receipt->invoiceProgram->clientprog->program->sub_prog ? $receipt->invoiceProgram->clientprog->program->sub_prog->sub_prog_name.' - ':''}}{{ $receipt->invoiceProgram->clientprog->program->prog_program }}</td>
                                        @elseif(isset($receipt->invb2b_id))
                                            @if($receipt->invoiceB2b->schprog_id))
                                                <td>{{ $receipt->invoiceB2b->sch_prog->program->sub_prog ? $receipt->invoiceB2b->sch_prog->program->sub_prog->sub_prog_name.' - ':''}}{{ $receipt->invoiceB2b->sch_prog->program->prog_program }}</td>
                                            @elseif((isset($receipt->invoiceB2b->partnerprog_id)))
                                                <td>{{ $receipt->invoiceB2b->partner_prog->program->sub_prog ? $receipt->invoiceB2b->partner_prog->program->sub_prog->sub_prog_name.' - ':''}}{{ $receipt->invoiceB2b->partner_prog->program->prog_program }}</td>
                                            @endif
                                        @endif 

                                        {{-- Method --}}
                                        <td>{{ $receipt->receipt_method }}</td>
                                        
                                        {{-- Installment --}}
                                        <td>Installment</td>
                                        
                                        {{-- Paid date --}}
                                        <td>{{ $receipt->created_at }}</td>

                                        {{-- Amount --}}
                                        <td>{{ $receipt->receipt_amount_idr }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Not yet invoice</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-light text-white">
                                <tr>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
