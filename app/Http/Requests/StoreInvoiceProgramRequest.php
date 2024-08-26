<?php

namespace App\Http\Requests;

use App\Http\Traits\CreateInvoiceIdTrait;
use App\Models\ClientProgram;
use App\Models\InvoiceProgram;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StoreInvoiceProgramRequest extends FormRequest
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

        if (in_array('idr', $currency) && $this->input('is_session') == "no") {
            return $this->rupiahInvoiceWithNoSession($currency); 

        } elseif (in_array('idr', $currency) && $this->input('is_session') == "yes") {
            
            return $this->rupiahInvoiceWithYesSession($currency);
        
        } elseif (in_array('other', $currency) && $this->input('is_session') == "no") {
            
            return $this->otherCurrencyInvoiceWithNoSession($currency);

        } elseif (in_array('other', $currency) && $this->input('is_session') == "yes") {

            return $this->otherCurrencyInvoiceWithYesSession($currency);

        }
    }

    protected function rupiahInvoiceWithYesSession($currency)
    {
        $clientProgId = $this->input('clientprog_id');
        $clientProgram = ClientProgram::find($clientProgId);

        $addQuery = $this->isMethod('POST') ? '|unique:tbl_inv,clientprog_id' : null;

        $addQueryInvDtlDueDateOther = $this->input('inv_paymentmethod') == 'installment' ? '|after_or_equal:inv_duedate' : null;

        return [
            'clientprog_id' => 'required|exists:tbl_client_prog,clientprog_id' . $addQuery,
            'currency' => [
                'required',
                // function ($attribute, $value, $fail) use ($clientProgram) {
                //     if ($index = array_search('other', $value) !== false)
                //         unset($value[$index]);

                //     if ($value[0] != $clientProgram->program->prog_payment)
                //         $fail('Based on payment program from master program, it should be '.strtoupper($clientProgram->program->prog_payment));
                // }
            ],
            // 'is_session' => [
            //     function ($attribute, $value, $fail) use ($clientProgram) {
            //         if ($clientProgram->program->prog_payment == "session" && $value == "no")
            //             $fail('Is session has to be "yes" based on master program session');
            //     }
            // ],
            'session__si' => Rule::requiredIf(in_array('idr', $currency)),
            'duration__si' => Rule::requiredIf(in_array('idr', $currency)),
            'inv_price_idr__si' => Rule::requiredIf(in_array('idr', $currency)),
            'inv_discount_idr__si' => Rule::requiredIf(in_array('idr', $currency)),
            'inv_totalprice_idr__si' => Rule::requiredIf(in_array('idr', $currency)),
            'inv_words_idr__si' => Rule::requiredIf(in_array('idr', $currency)),
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

    protected function otherCurrencyInvoiceWithYesSession($currency)
    {
        $clientProgId = $this->input('clientprog_id');
        $clientProgram = ClientProgram::find($clientProgId);

        $addQuery = $this->isMethod('POST') ? '|unique:tbl_inv,clientprog_id' : null;

        $addQueryInvDtlDueDateOther = $this->input('inv_paymentmethod') == 'installment' ? '|after_or_equal:inv_duedate' : null;
        $rules = [
            'clientprog_id' => 'required|exists:tbl_client_prog,clientprog_id' . $addQuery,
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
            'inv_price__so' => Rule::requiredIf(in_array('other', $currency)),
            'session__so' => Rule::requiredIf(in_array('other', $currency)),
            'duration__so' => Rule::requiredIf(in_array('other', $currency)),
            // 'inv_earlybird__so' => Rule::requiredIf(in_array('other', $currency)),
            // 'inv_discount__so' => Rule::requiredIf(in_array('other', $currency)),
            'inv_totalprice__so' => Rule::requiredIf(in_array('other', $currency)),
            'inv_words__so' => Rule::requiredIf(in_array('other', $currency)),
            'inv_price_idr__so' => Rule::requiredIf(in_array('idr', $currency)),
            // 'inv_earlybird_idr__so' => Rule::requiredIf(in_array('idr', $currency)),
            // 'inv_discount_idr__so' => Rule::requiredIf(in_array('idr', $currency)),
            'inv_totalprice_idr__so' => Rule::requiredIf(in_array('idr', $currency)),
            'inv_words_idr__so' => Rule::requiredIf(in_array('idr', $currency)),
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
            'invdtl_duedate__other.*' => 'required_if:inv_paymentmethod,installment'.$addQueryInvDtlDueDateOther,
            'invdtl_percentage__other.*' => 'required_if:inv_paymentmethod,installment',
            'invdtl_amountidr__other.*' => 'required_if:inv_paymentmethod,installment',
        ];



        return $rules;
    }

    protected function otherCurrencyInvoiceWithNoSession($currency)
    {
        $clientProgId = $this->input('clientprog_id');
        $clientProgram = ClientProgram::find($clientProgId);

        $addQuery = $this->isMethod('POST') ? '|unique:tbl_inv,clientprog_id' : null;

        $rules = [
            'clientprog_id' => 'required|exists:tbl_client_prog,clientprog_id' . $addQuery,
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
        $clientProgId = $this->input('clientprog_id');
        $clientProgram = ClientProgram::find($clientProgId);

        $last_id = InvoiceProgram::whereMonth('created_at', date('m'))->max(DB::raw('substr(inv_id, 1, 4)'));

        # Use Trait Create Invoice Id
        $inv_id = $this->getInvoiceId($last_id, $clientProgram->prog_id);

        $addQuery = $this->isMethod('POST') ? '|unique:tbl_inv,clientprog_id' : null;

        $addQueryInvDtlDueDateOther = $this->input('inv_paymentmethod') == 'installment' ? '|after_or_equal:inv_duedate' : null;

        return [
            'clientprog_id' => 'required|exists:tbl_client_prog,clientprog_id'.$addQuery,
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