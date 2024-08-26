<?php

namespace App\Http\Requests;

use App\Http\Traits\CreateInvoiceIdTrait;
use App\Models\Bundling;
use App\Models\ClientProgram;
use App\Models\InvoiceProgram;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StoreInvoiceProgramBundleRequest extends FormRequest
{
    use CreateInvoiceIdTrait;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        $currency = [];
        $currency[0] = $this->input('currency');
        $currency[1] = $this->input('currency_detail');

        if (in_array('idr', $currency)) {
            return $this->rupiahInvoiceWithNoSession($currency); 

        } elseif (in_array('other', $currency)) {
            
            return $this->otherCurrencyInvoiceWithNoSession($currency);
        }
    }

    protected function otherCurrencyInvoiceWithNoSession($currency)
    {
        $bundlingId = $this->input('bundling_id');
        $bundle = Bundling::where('uuid', $bundlingId)->first();

        $addQuery = $this->isMethod('POST') ? '|unique:tbl_inv,bundling_id' : null;

        $rules = [
            'bundling_id' => 'required|exists:tbl_bundling,uuid'.$addQuery,
            'currency' => [
                'required',
                // function ($attribute, $value, $fail) use ($clientProgram) {
                //     $currency = null;
                //     foreach ($value as $key => $val) {
                //         if ($val != NULL)
                //             $currency = $val != "other" ? $val : null;
                //     }

                //     if ($currency != $clientProgram->program->prog_payment)
                //         $fail('Based on payment program from master program, it should be '.strtoupper($clientProgram->program->prog_payment));
                // }
            ],
            // 'is_session' => [
            //     function ($attribute, $value, $fail) use ($clientProgram) {
            //         if ($clientProgram->program->prog_payment == "session" && $value == "no")
            //             $fail('Is session has to be "yes" based on master program session');
            //     }
            // ],
            'curs_rate' => 'required',
            'inv_price__nso' => Rule::requiredIf(in_array('other', $currency)),
            // 'inv_earlybird__nso' => Rule::requiredIf(in_array('other', $currency)),
            // 'inv_discount__nso' => Rule::requiredIf(in_array('other', $currency)),
            'inv_totalprice__nso' => Rule::requiredIf(in_array('other', $currency)),
            'inv_words__nso' => Rule::requiredIf(in_array('other', $currency)),
            'inv_price_idr__nso' => Rule::requiredIf(in_array('idr', $currency)),
            // 'inv_earlybird_idr__nso' => Rule::requiredIf(in_array('idr', $currency)),
            // 'inv_discount_idr__nso' => Rule::requiredIf(in_array('idr', $currency)),
            'inv_totalprice_idr__nso' => Rule::requiredIf(in_array('idr', $currency)),
            'inv_words_idr__nso' => Rule::requiredIf(in_array('idr', $currency)),
            'inv_paymentmethod' => 'required|in:full,installment',
            'invoice_date' => 'required',
            'inv_duedate' => 'date|required_if:inv_paymentmethod,full',
            'inv_notes' => 'nullable',
            'inv_tnc' => 'nullable',

            # installment validation
            'invdtl_installment__other.*' => [
                'required_if:inv_paymentmethod,installment',
                'distinct',
                // Rule::unique('tbl_invdtl', 'invdtl_installment')
                //     ->where(function ($query) use ($inv_id) {
                //         return $query->where('inv_id', $inv_id);
                // })
            ],
            'invdtl_duedate__other.*' => 'required_if:inv_paymentmethod,installment',
            'invdtl_percentage__other.*' => 'required_if:inv_paymentmethod,installment',
            'invdtl_amountidr__other.*' => 'required_if:inv_paymentmethod,installment',
        ];

        if ($this->input('inv_duedate') != NULL && $this->input('inv_paymentmethod') == 'installment')
            $rules['invdtl_duedate__other.*'] .= '|after_or_equal:inv_duedate';

        return $rules;
    }

    protected function rupiahInvoiceWithNoSession($currency)
    {
        $bundlingId = $this->input('bundling_id');
        $bundle = Bundling::where('uuid', $bundlingId)->first();

        $last_id = InvoiceProgram::whereMonth('created_at', date('m'))->max(DB::raw('substr(inv_id, 1, 4)'));

        # Use Trait Create Invoice Id
        $inv_id = $this->getInvoiceId($last_id, 'BDL');

        $addQuery = $this->isMethod('POST') ? '|unique:tbl_inv,bundling_id' : null;

        $addQueryInvDtlDueDateOther = $this->input('inv_paymentmethod') == 'installment' ? '|after_or_equal:inv_duedate' : null;

        return [
            'bundling_id' => 'required|exists:tbl_bundling,uuid'.$addQuery,
            'currency' => 'required',
            // 'is_session' => [
            //     function ($attribute, $value, $fail) use ($clientProgram) {
            //         if ($clientProgram->program->prog_payment == "session" && $value == "no")
            //             $fail('Is session has to be "yes" based on master program session');
            //     }
            // ],
            'inv_price_idr' => Rule::requiredIf(in_array('idr', $currency)),
            // 'inv_earlybird_idr' => Rule::requiredIf(in_array('idr', $currency)),
            // 'inv_discount_idr' => Rule::requiredIf(in_array('idr', $currency)),
            'inv_totalprice_idr' => Rule::requiredIf(in_array('idr', $currency)),
            'inv_words_idr' => Rule::requiredIf(in_array('idr', $currency)),
            'inv_paymentmethod' => 'required|in:full,installment',
            'invoice_date' => 'required',
            'inv_duedate' => 'date|required_if:inv_paymentmethod,full',
            'inv_notes' => 'nullable',
            'inv_tnc' => 'nullable',

            # installment validation
            'invdtl_installment.*' => [
                'required_if:inv_paymentmethod,installment',
                'distinct',
                // Rule::unique('tbl_invdtl', 'invdtl_installment')
                //     ->where(function ($query) use ($inv_id) {
                //         return $query->where('inv_id', $inv_id);
                // })
            ],
            'invdtl_duedate.*' => 'required_if:inv_paymentmethod,installment'.$addQueryInvDtlDueDateOther,
            'invdtl_percentage.*' => 'required_if:inv_paymentmethod,installment',
            'invdtl_amountidr.*' => 'required_if:inv_paymentmethod,installment',
        ];
    }
}