<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use Illuminate\Support\Facades\Request;

class StoreInvoiceSchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;

    public function __construct(InvoiceB2bRepositoryInterface $invoiceB2bRepository)
    {      
        $this->invoiceB2bRepository = $invoiceB2bRepository;
    }

    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'invb2b_price' => 'Price',
            'invb2b_priceidr_other' => 'Price',
            'invb2b_priceidr' => 'Price',
            'invb2b_participants' => 'Participants',
            'invb2b_participants_other' => 'Participants',
            'invb2b_disc' => 'Discount',
            'invb2b_discidr_other' => 'Discount',
            'invb2b_discidr' => 'Discount',
            'invb2b_totprice' => 'Total price',
            'invb2b_totpriceidr_other' => 'Total price',
            'invb2b_totpriceidr' => 'Total price',
            'invb2b_words' => 'Words',
            'invb2b_wordsidr_other' => 'Words',
            'invb2b_wordsidr' => 'Words',
            'invb2b_date' => 'Invoice date',
            'invb2b_duedate' => 'Invoice due date',
            'invb2b_pm' => 'Payment Method',
            'invb2b_notes' => 'Notes',
            'invb2b_tnc' => 'Terms & Condition'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return $this->isMethod('POST') ? $this->store() : $this->update();


    }

    protected function store()
    {
        return [
            'invb2b_price' => 'required_if:select_currency,other|integer|nullable',
            'invb2b_priceidr_other' => 'required_if:select_currency,other|integer|nullable',
            'invb2b_priceidr' => 'required_if:select_currency,idr|integer|nullable',
            'invb2b_participants' => 'required_if:select_currency,idr|integer|nullable',
            'invb2b_participants_other' => 'required_if:select_currency,other|integer|nullable',
            'invb2b_disc' => 'required_if:select_currency,other|integer|nullable',
            'invb2b_discidr_other' => 'required_if:select_currency,other|integer|nullable',
            'invb2b_discidr' => 'required_if:select_currency,idr|integer|nullable',
            'invb2b_totprice' => 'required_if:select_currency,other|integer|nullable',
            'invb2b_totpriceidr_other' => 'required_if:select_currency,other|integer|nullable',
            'invb2b_totpriceidr' => 'required_if:select_currency,idr|integer|nullable',
            'invb2b_words' => 'required_if:select_currency,other|nullable',
            'invb2b_wordsidr_other' => 'required_if:select_currency,other|nullable',
            'invb2b_wordsidr' => 'required_if:select_currency,idr|nullable',
            'invb2b_date' => 'required|date|before_or_equal:invb2b_duedate',
            'invb2b_duedate' => 'required|date|after_or_equal:invb2b_date',
            'invb2b_pm' => 'required|in:full,installment',
            'invb2b_notes' => 'nullable',
            'invb2b_tnc' => 'nullable',
            'curs_rate' => 'required_if:select_currency,other|integer|nullable',
            'currency' => 'required_if:select_currency,other|in:gbp,usd,sgd|nullable',
            'invdtl_installment.*' => function ($attribute, $value, $fail) {
                if($this->input('select_currency') == 'idr'){
                    if ($this->input('invb2b_pm') === 'installment') {
                        if($value == null){
                            $fail('The '.$attribute.' is required.');
                        }
                    }
                }
            },
            'invdtl_installment_other.*' => function ($attribute, $value, $fail) {
                if($this->input('select_currency') == 'other'){
                    if ($this->input('invb2b_pm') === 'installment') {
                        if($value == null){
                            $fail('The '.$attribute.' is required.');
                        }
                    }
                }
            },
            // 'invdtl_installment.*' => [Rule::when('invdtl_installment', 'required', function ($input) {
            //     return $this->input('select_currency') == 'idr' && $this->input('invb2b_pm') == 'installment';
            // })],
            // 'invdtl_installment_other.*' => [Rule::when('invdtl_installment_other', 'required', function ($input) {
            //     return $input->select_currency == 'other' && $input->invb2b_pm == 'installment';
            // })],
            // 'invdtl_duedate.*' => [Rule::when('invdtl_duedate.*', function ($input) {
            //     return request()->select_currency == 'idr' && request()->invb2b_pm == 'installment';
            // })],
            // 'invdtl_duedate.*' => 'required_unless:invb2b_pm,full|required_if:select_currency,idr|date|before_or_equal:invb2b_duedate|after_or_equal:invb2b_date|nullable',
            // 'invdtl_duedate_other.*' => 'required_unless:invb2b_pm,full|required_if:select_currency,other|required_with:invdtl_pm|date|before_or_equal:invb2b_duedate|after_or_equal:invb2b_date|nullable',
            'invdtl_duedate.*' =>  [function ($attribute, $value, $fail) {
                if($this->input('select_currency') == 'idr'){
                    if ($this->input('invb2b_pm') == 'installment') {
                        if($value == null){
                            $fail('The '.$attribute.' is required.');
                        }
                    }
                }
            }, 'date','before_or_equal:invb2b_duedate','after_or_equal:invb2b_date'],

            'invdtl_duedate_other.*' => [function ($attribute, $value, $fail) {
                if($this->input('select_currency') == 'other'){
                    if ($this->input('invb2b_pm') == 'installment') {
                        if($value == null){
                            $fail('The '.$attribute.' is required.');
                        }
                    }
                }
            }, 'date','before_or_equal:invb2b_duedate','after_or_equal:invb2b_date'],
            
      
        ];

    }

    protected function update()
    {
        $invb2b_num = $this->route('detail');
        $invoiceB2b = $this->invoiceB2bRepository->getInvoiceB2bById($invb2b_num);
        return [
            'invb2b_price' => 'required_if:select_currency,other|integer|nullable',
            'invb2b_priceidr_other' => 'required_if:select_currency,other|integer|nullable',
            'invb2b_priceidr' => 'required_if:select_currency,idr|integer|nullable',
            'invb2b_participants' => 'required_if:select_currency,idr|integer|nullable',
            'invb2b_participants_other' => 'required_if:select_currency,other|integer|nullable',
            'invb2b_disc' => 'required_if:select_currency,other|integer|nullable',
            'invb2b_discidr_other' => 'required_if:select_currency,other|integer|nullable',
            'invb2b_discidr' => 'required_if:select_currency,idr|integer|nullable',
            'invb2b_totprice' => 'required_if:select_currency,other|integer|nullable',
            'invb2b_totpriceidr_other' => 'required_if:select_currency,other|integer|nullable',
            'invb2b_totpriceidr' => 'required_if:select_currency,idr|integer|nullable',
            'invb2b_words' => 'required_if:select_currency,other|nullable',
            'invb2b_wordsidr_other' => 'required_if:select_currency,other|nullable',
            'invb2b_wordsidr' => 'required_if:select_currency,idr|nullable',
            'invb2b_date' => 'required|date|before_or_equal:invb2b_duedate',
            'invb2b_duedate' => 'required|date|after_or_equal:invb2b_date',
            'invb2b_pm' => 'required|in:full,installment',
            'invb2b_notes' => 'nullable',
            'invb2b_tnc' => 'nullable',
            'curs_rate' => 'required_if:select_currency,other|integer|nullable',
            'currency' => 'required_if:select_currency,other|in:gbp,usd,sgd|nullable',
            // 'invdtl_installment.*' => 'required_if:invb2b_pm,installment|nullable',
            // 'invdtl_installment.*' => ['required_if:invb2b_pm,installment','nullable', Rule::unique('tbl_invdtl', 'invdtl_installment')->where(fn ($query) => $query->where('invb2b_id', $invoiceB2b->invb2b_id))],
            // 'invdtl_installment_other.*' => 'required_if:invb2b_pm,installment|nullable',
            // 'invdtl_duedate.*' => 'required_unless:invb2b_pm,installment|required_if:select_currency,idr|date|before_or_equal:invb2b_duedate|after_or_equal:invb2b_date|nullable',
            // 'invdtl_duedate_other.*' => 'required_unless:invb2b_pm,full|required_if:select_currency,other|required_with:invdtl_pm|date|before_or_equal:invb2b_duedate|after_or_equal:invb2b_date|nullable',
            'invdtl_installment.*' => function ($attribute, $value, $fail) {
                if($this->input('select_currency') == 'idr'){
                    if ($this->input('invb2b_pm') === 'installment') {
                        if($value == null){
                            $fail('The '.$attribute.' is required.');
                        }
                    }
                }
            },
            'invdtl_installment_other.*' => function ($attribute, $value, $fail) {
                if($this->input('select_currency') == 'other'){
                    if ($this->input('invb2b_pm') === 'installment') {
                        if($value == null){
                            $fail('The '.$attribute.' is required.');
                        }
                    }
                }
            },
            'invdtl_duedate.*' =>  [function ($attribute, $value, $fail) {
                if($this->input('select_currency') == 'idr'){
                    if ($this->input('invb2b_pm') == 'installment') {
                        if($value == null){
                            $fail('The '.$attribute.' is required.');
                        }
                    }
                }
            }, 'date','before_or_equal:invb2b_duedate','after_or_equal:invb2b_date'],

            'invdtl_duedate_other.*' => [function ($attribute, $value, $fail) {
                if($this->input('select_currency') == 'other'){
                    if ($this->input('invb2b_pm') == 'installment') {
                        if($value == null){
                            $fail('The '.$attribute.' is required.');
                        }
                    }
                }
            }, 'date','before_or_equal:invb2b_duedate','after_or_equal:invb2b_date'],

      
        ];
    }
}
