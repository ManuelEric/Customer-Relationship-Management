@extends('layout.main')

@section('title', 'Lead Tracking')

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(function() {
            $('input[name="daterange"]').daterangepicker({
                // minDate: moment('2024-11-01').format('L'),
                startDate: moment().startOf('L'),
                endDate: moment().startOf('L').add(7, 'days'),
            }, function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end
                    .format('YYYY-MM-DD'));
            });
        });
    </script>
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col">
            <div class="alert alert-success">
                These report between <u>{{ date('F, dS Y', strtotime($start_date)) }}</u> and
                <u>{{ date('F, dS Y', strtotime($end_date)) }}</u>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">
                        Filter
                    </h5>
                </div>
                <div class="card-body">
                    <form action="" method="GET">
                        <div class="mb-3">
                            <label for="daterange">Date Range</label>
                            <input type="text" name="daterange" value=""
                                class="form-control form-control-sm text-center" id="daterange" />
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-search me-1"></i>
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            {{-- Lead Summary  --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        Leads Summary
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="text-center bg-secondary text-white">
                                <th rowspan="2" width="30%">Topic</th>
                                <th colspan="2" width="20%">Online</th>
                                <th rowspan="2" width="10%">Offline</th>
                                <th rowspan="2" width="15%">Referral from Existing Client</th>
                                <th rowspan="2" width="10%">Total</th>
                                <th rowspan="2" width="15%">Conversion Rate</th>
                            </tr>
                            <tr class="text-center bg-secondary text-white">
                                <th>Paid</th>
                                <th>Organic</th>
                            </tr>
                        </thead>
                        <thead>
                            @foreach ($lead_summary as $title => $summary)
                            <tr class="text-center">
                                <th class="bg-secondary text-white">{{ ucwords(str_replace('_', ' ', $title)) }}</th>
                                @foreach ($summary as $detail)
                                <td>{{ $detail }}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </thead>
                    </table>
                </div>
            </div>

            {{-- Lead by Product --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        Leads by Product
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="mentoring-tab" data-bs-toggle="tab"
                                data-bs-target="#mentoring-tab-pane" type="button" role="tab"
                                aria-controls="mentoring-tab-pane" aria-selected="true">Admission Mentoring</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tutoring-tab" data-bs-toggle="tab"
                                data-bs-target="#tutoring-tab-pane" type="button" role="tab"
                                aria-controls="tutoring-tab-pane" aria-selected="false">Academic Tutoring</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="gip-tab" data-bs-toggle="tab" data-bs-target="#gip-tab-pane"
                                type="button" role="tab" aria-controls="gip-tab-pane"
                                aria-selected="false">GIP</button>
                        </li>
                    </ul>
                    <div class="tab-content overflow-auto" style="max-height: 450px" id="myTabContent">
                        {{-- MENTORING  --}}
                        <div class="tab-pane fade show active" id="mentoring-tab-pane" role="tabpanel"
                            aria-labelledby="home-tab" tabindex="0">

                            {{-- Product  --}}
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center bg-secondary text-white">
                                        <th rowspan="2" width="30%">Topic</th>
                                        <th colspan="2" width="20%">Online</th>
                                        <th rowspan="2" width="10%">Offline</th>
                                        <th rowspan="2" width="15%">Referral from Existing Client</th>
                                        <th rowspan="2" width="10%">Total</th>
                                        <th rowspan="2" width="15%">Conversion Rate</th>
                                    </tr>
                                    <tr class="text-center bg-secondary text-white">
                                        <th>Paid</th>
                                        <th>Organic</th>
                                    </tr>
                                </thead>
                                <thead>
                                    @foreach ($lead_by_product['mentoring'] as $title => $summary)
                                    <tr class="text-center">
                                        <th class="bg-secondary text-white">{{ ucwords(str_replace('_', ' ', $title)) }}</th>
                                        @foreach ($summary as $detail)
                                        <td>{{ $detail }}</td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </thead>
                            </table>

                            {{-- Sales PIC  --}}
                            <table class="table table-bordered mt-4">
                                <thead>
                                    <tr class="bg-secondary text-white text-center">
                                        <th>#</th>
                                        <th>Potential<br>Leads</th>
                                        <th>Assessment<br>Form</th>
                                        <th>IC</th>
                                        <th>IAR/IA </th>
                                        <th>Deal</th>
                                        <th>Agreement</th>
                                        <th>Payment</th>
                                    </tr>
                                    @php
                                        $total_potential = $total_assessment = $total_ic = $total_ia = $total_deal = $total_agreement = $total_payment = 0;
                                        $total_potential_cr = $total_assessment_cr = $total_ic_cr = $total_ia_cr = $total_deal_cr = $total_agreement_cr = $total_payment_cr = 0;
                                    @endphp
                                    @foreach ($mapped_sales as $sales)
                                    @php
                                        //! calculate the lead
                                        $id = $sales['id'];
                                        $each_potential = array_key_exists($id, $lead_by_sales['mentoring']['potential_leads']->toArray()) ? count($lead_by_sales['mentoring']['potential_leads']->toArray()[$id]) : 0;
                                        $each_assessment = array_key_exists($id, $lead_by_sales['mentoring']['assessment_form']->toArray()) ? count($lead_by_sales['mentoring']['assessment_form']->toArray()[$id]) : 0;
                                        $each_ic = array_key_exists($id, $lead_by_sales['mentoring']['IC']->toArray()) ? count($lead_by_sales['mentoring']['IC']->toArray()[$id]) : 0;
                                        $each_ia = array_key_exists($id, $lead_by_sales['mentoring']['IA']->toArray()) ? count($lead_by_sales['mentoring']['IA']->toArray()[$id]) : 0;
                                        $each_deal = array_key_exists($id, $lead_by_sales['mentoring']['deal']->toArray()) ? count($lead_by_sales['mentoring']['deal']->toArray()[$id]) : 0;
                                        $each_agreement = array_key_exists($id, $lead_by_sales['mentoring']['agreement']->toArray()) ? count($lead_by_sales['mentoring']['agreement']->toArray()[$id]) : 0;
                                        $each_payment = array_key_exists($id, $lead_by_sales['mentoring']['payment']->toArray()) ? count($lead_by_sales['mentoring']['payment']->toArray()[$id]) : 0;

                                        $total_potential += $each_potential;
                                        $total_assessment += $each_assessment;
                                        $total_ic += $each_ic;
                                        $total_ia += $each_ia;
                                        $total_deal += $each_deal;
                                        $total_agreement += $each_agreement;
                                        $total_payment += $each_payment;

                                        //! calculate the conversion rate
                                        $each_assessment_cr = toPercentage($each_potential, $each_assessment);
                                        $each_ic_cr = toPercentage($each_assessment, $each_ic);
                                        $each_ia_cr = toPercentage($each_ic, $each_ia);
                                        $each_deal_cr = toPercentage($each_ia, $each_deal);
                                        $each_agreement_cr = toPercentage($each_deal, $each_agreement);
                                        $each_payment_cr = toPercentage($each_agreement, $each_payment);

                                        $total_assessment_cr = toPercentage($total_potential, $total_assessment);
                                        $total_ic_cr = toPercentage($total_assessment, $total_ic);
                                        $total_ia_cr = toPercentage($total_ic, $total_ia);
                                        $total_deal_cr = toPercentage($total_ia, $total_deal);
                                        $total_agreement_cr = toPercentage($total_deal, $total_agreement);
                                        $total_payment_cr = toPercentage($total_agreement, $total_payment);
                                    @endphp
                                    <tr class="text-center">
                                        <th class="bg-secondary text-white">{{ $sales['name'] }}</th>
                                        <td>{{ $each_potential }}</td>
                                        <td>{{ $each_assessment }}</td>
                                        <td>{{ $each_ic }}</td>
                                        <td>{{ $each_ia }}</td>
                                        <td>{{ $each_deal }}</td>
                                        <td>{{ $each_agreement }}</td>
                                        <td>{{ $each_payment }}</td>
                                    </tr>
                                    <tr class="text-center">
                                        <th class="bg-secondary text-white">{{ $sales['name'] }} CR</th>
                                        <td class="bg-secondary"></td><!-- potential -->
                                        <td>{{ $each_assessment_cr }}%</td><!-- assessment form -->
                                        <td>{{ $each_ic_cr }}%</td><!-- ic -->
                                        <td>{{ $each_ia_cr }}%</td><!-- ia -->
                                        <td>{{ $each_deal_cr }}%</td><!-- deal -->
                                        <td>{{ $each_agreement_cr }}%</td><!-- agreement -->
                                        <td>{{ $each_payment_cr }}%</td><!-- payment -->
                                    </tr>
                                    @endforeach
                                    <tr class="text-center">
                                        <th class="bg-secondary text-white">Total</th>
                                        <td>{{ $total_potential }}</td>
                                        <td>{{ $total_assessment }}</td>
                                        <td>{{ $total_ic }}</td>
                                        <td>{{ $total_ia }}</td>
                                        <td>{{ $total_deal }}</td>
                                        <td>{{ $total_agreement }}</td>
                                        <td>{{ $total_payment }}</td>
                                    </tr>
                                    <tr class="text-center">
                                        <th class="bg-secondary text-white">Conversion Rate</th>
                                        <td class="bg-secondary"></td>
                                        <td>{{ $total_assessment_cr }}%</td>
                                        <td>{{ $total_ic_cr }}%</td>
                                        <td>{{ $total_ia_cr }}%</td>
                                        <td>{{ $total_deal_cr }}%</td>
                                        <td>{{ $total_deal_cr }}%</td>
                                        <td>{{ $total_payment_cr }}%</td>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                        {{-- TUTORING  --}}
                        <div class="tab-pane fade" id="tutoring-tab-pane" role="tabpanel" aria-labelledby="tutoring-tab"
                            tabindex="0">
                            {{-- Product  --}}
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center bg-secondary text-white">
                                        <th rowspan="2" width="30%">Topic</th>
                                        <th colspan="2" width="20%">Online</th>
                                        <th rowspan="2" width="10%">Offline</th>
                                        <th rowspan="2" width="15%">Referral from Existing Client</th>
                                        <th rowspan="2" width="10%">Total</th>
                                        <th rowspan="2" width="15%">Conversion Rate</th>
                                    </tr>
                                    <tr class="text-center bg-secondary text-white">
                                        <th>Paid</th>
                                        <th>Organic</th>
                                    </tr>
                                </thead>
                                <thead>
                                    @foreach ($lead_by_product['tutoring'] as $title => $summary)
                                    <tr class="text-center">
                                        <th class="bg-secondary text-white">{{ ucwords(str_replace('_', ' ', $title)) }}</th>
                                        @foreach ($summary as $detail)
                                        <td>{{ $detail }}</td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </thead>
                            </table>

                            {{-- Sales PIC  --}}
                            <table class="table table-bordered mt-4">
                                <thead>
                                    <tr class="bg-secondary text-white text-center">
                                        <th>#</th>
                                        <th>Potential Leads</th>
                                        <th>Trial Date</th>
                                        <th>Deal</th>
                                        <th>Payment</th>
                                    </tr>
                                    @php
                                        $total_potential = $total_trial_date = $total_deal = $total_payment = 0;
                                        $total_potential_cr = $total_trial_date_cr = $total_deal_cr = $total_payment_cr = 0;
                                    @endphp
                                    @foreach ($mapped_sales as $sales)
                                    @php
                                        //! calculate the lead
                                        $id = $sales['id'];
                                        $each_potential = array_key_exists($id, $lead_by_sales['tutoring']['potential_leads']->toArray()) ? count($lead_by_sales['tutoring']['potential_leads']->toArray()[$id]) : 0;
                                        $each_trial_date = array_key_exists($id, $lead_by_sales['tutoring']['trial_date']->toArray()) ? count($lead_by_sales['tutoring']['trial_date']->toArray()[$id]) : 0;
                                        $each_deal = array_key_exists($id, $lead_by_sales['tutoring']['deal']->toArray()) ? count($lead_by_sales['tutoring']['deal']->toArray()[$id]) : 0;
                                        $each_payment = array_key_exists($id, $lead_by_sales['tutoring']['payment']->toArray()) ? count($lead_by_sales['tutoring']['payment']->toArray()[$id]) : 0;

                                        $total_potential += $each_potential;
                                        $total_trial_date += $each_trial_date;
                                        $total_deal += $each_deal;
                                        $total_payment += $each_payment;

                                        //! calculate the conversion rate
                                        $each_trial_date_cr = toPercentage($each_potential, $each_trial_date);
                                        $each_deal_cr = toPercentage($each_trial_date, $each_deal);
                                        $each_payment_cr = toPercentage($each_deal, $each_payment);

                                        $total_trial_date_cr = toPercentage($total_potential, $total_trial_date);
                                        $total_deal_cr = toPercentage($total_trial_date, $total_deal);
                                        $total_payment_cr = toPercentage($total_deal, $total_payment);
                                    @endphp
                                    <tr class="text-center">
                                        <th class="bg-secondary text-white">{{ $sales['name'] }}</th>
                                        <td>{{ $each_potential }}</td>
                                        <td>{{ $each_trial_date }}</td>
                                        <td>{{ $each_deal }}</td>
                                        <td>{{ $each_payment }}</td>
                                    </tr>
                                    <tr class="text-center">
                                        <th class="bg-secondary text-white">{{ $sales['name'] }} CR</th>
                                        <td class="bg-secondary"></td><!-- potential -->
                                        <td>{{ $each_trial_date_cr }}%</td><!-- trial_date -->
                                        <td>{{ $each_deal_cr }}%</td><!-- deal -->
                                        <td>{{ $each_payment_cr }}%</td><!-- payment -->
                                    </tr>
                                    @endforeach
                                    <tr class="text-center">
                                        <th class="bg-secondary text-white">Total</th>
                                        <td>{{ $total_potential }}</td>
                                        <td>{{ $total_trial_date }}</td>
                                        <td>{{ $total_deal }}</td>
                                        <td>{{ $total_payment }}</td>
                                    </tr>
                                    <tr class="text-center">
                                        <th class="bg-secondary text-white">Conversion Rate</th>
                                        <td class="bg-secondary"></td>
                                        <td>{{ $total_trial_date_cr }}%</td>
                                        <td>{{ $total_deal_cr }}%</td>
                                        <td>{{ $total_payment_cr }}%</td>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                        {{-- GIP  --}}
                        <div class="tab-pane fade" id="gip-tab-pane" role="tabpanel" aria-labelledby="gip-tab"
                            tabindex="0">
                            {{-- Product  --}}
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center bg-secondary text-white">
                                        <th rowspan="2" width="30%">Topic</th>
                                        <th colspan="2" width="20%">Online</th>
                                        <th rowspan="2" width="10%">Offline</th>
                                        <th rowspan="2" width="15%">Referral from Existing Client</th>
                                        <th rowspan="2" width="10%">Total</th>
                                        <th rowspan="2" width="15%">Conversion Rate</th>
                                    </tr>
                                    <tr class="text-center bg-secondary text-white">
                                        <th>Paid</th>
                                        <th>Organic</th>
                                    </tr>
                                </thead>
                                <thead>
                                    @foreach ($lead_by_product['gip'] as $title => $summary)
                                    <tr class="text-center">
                                        <th class="bg-secondary text-white">{{ ucwords(str_replace('_', ' ', $title)) }}</th>
                                        @foreach ($summary as $detail)
                                        <td>{{ $detail }}</td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </thead>
                            </table>

                            {{-- Sales PIC  --}}
                            <table class="table table-bordered mt-4">
                                <thead>
                                    <tr class="bg-secondary text-white text-center">
                                        <th>#</th>
                                        <th>Potential Leads</th>
                                        <th>Deal</th>
                                        <th>Payment</th>
                                    </tr>
                                    @php
                                        $total_potential = $total_deal = $total_payment = 0;
                                        $total_potential_cr = $total_deal_cr = $total_payment_cr = 0;
                                    @endphp
                                    @foreach ($mapped_sales as $sales)
                                    @php
                                        //! calculate the lead
                                        $id = $sales['id'];
                                        $each_potential = array_key_exists($id, $lead_by_sales['gip']['potential_leads']->toArray()) ? count($lead_by_sales['gip']['potential_leads']->toArray()[$id]) : 0;
                                        $each_deal = array_key_exists($id, $lead_by_sales['gip']['deal']->toArray()) ? count($lead_by_sales['gip']['deal']->toArray()[$id]) : 0;
                                        $each_payment = array_key_exists($id, $lead_by_sales['gip']['payment']->toArray()) ? count($lead_by_sales['gip']['payment']->toArray()[$id]) : 0;

                                        $total_potential += $each_potential;
                                        $total_deal += $each_deal;
                                        $total_payment += $each_payment;

                                        //! calculate the conversion rate
                                        $each_deal_cr = toPercentage($each_potential, $each_deal);
                                        $each_payment_cr = toPercentage($each_deal, $each_payment);

                                        $total_deal_cr = toPercentage($total_potential, $total_deal);
                                        $total_payment_cr = toPercentage($total_deal, $total_payment);
                                    @endphp
                                    <tr class="text-center">
                                        <th class="bg-secondary text-white">{{ $sales['name'] }}</th>
                                        <td>{{ $each_potential }}</td>
                                        <td>{{ $each_deal }}</td>
                                        <td>{{ $each_payment }}</td>
                                    </tr>
                                    <tr class="text-center">
                                        <th class="bg-secondary text-white">{{ $sales['name'] }} CR</th>
                                        <td class="bg-secondary"></td><!-- potential -->
                                        <td>{{ $each_deal_cr }}%</td><!-- deal -->
                                        <td>{{ $each_payment_cr }}%</td><!-- payment -->
                                    </tr>
                                    @endforeach
                                    <tr class="text-center">
                                        <th class="bg-secondary text-white">Total</th>
                                        <td>{{ $total_potential }}</td>
                                        <td>{{ $total_deal }}</td>
                                        <td>{{ $total_payment }}</td>
                                    </tr>
                                    <tr class="text-center">
                                        <th class="bg-secondary text-white">Conversion Rate</th>
                                        <td class="bg-secondary"></td>
                                        <td>{{ $total_deal_cr }}%</td>
                                        <td>{{ $total_payment_cr }}%</td>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
