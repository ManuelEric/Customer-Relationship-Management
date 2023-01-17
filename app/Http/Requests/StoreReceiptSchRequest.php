<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Request;

class StoreReceiptSchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function authorize()
    {
        return true;
    }

    // public function attributes()
    // {
    //     return [
    //         'invb2b_price' => 'Price',
    //         'invb2b_priceidr_other' => 'Price',
    //         'invb2b_priceidr' => 'Price',
    //         'invb2b_participants' => 'Participants',
    //         'invb2b_participants_other' => 'Participants',
    //         'invb2b_disc' => 'Discount',
    //         'invb2b_discidr_other' => 'Discount',
    //         'invb2b_discidr' => 'Discount',
    //         'invb2b_totprice' => 'Total price',
    //         'invb2b_totpriceidr_other' => 'Total price',
    //         'invb2b_totpriceidr' => 'Total price',
    //         'invb2b_words' => 'Words',
    //         'invb2b_wordsidr_other' => 'Words',
    //         'invb2b_wordsidr' => 'Words',
    //         'invb2b_date' => 'Invoice date',
    //         'invb2b_duedate' => 'Invoice due date',
    //         'invb2b_pm' => 'Payment Method',
    //         'invb2b_notes' => 'Notes',
    //         'invb2b_tnc' => 'Terms & Condition',
    //         'invdtl_installment.*' => 'Installment Name',
    //         'invdtl_installment_other.*' => 'Installment Name',
    //         'invdtl_duedate.*' => 'Installment Due Date',
    //         'invdtl_duedate_other.*' => 'Installment Due Date',
    //         'invdtl_percentage.*' => 'Installment Percentage',
    //         'invdtl_percentage_other.*' => 'Installment Percentage',
    //         'invdtl_amountidr.*' => 'Installment Amount',
    //         'invdtl_amount_other.*' => 'Installment Amount',
    //         'invdtl_amountidr_other.*' => 'Installment Amount',
    //     ];
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        // return $this->isMethod('POST') ? $this->store() : $this->update();
        return $this->input('select_currency_receipt') == 'idr' ? $this->idr() : $this->other();
    }

    protected function idr()
    {
        return [
            'receipt_amount_idr' => 'required|integer',
            'receipt_words_idr' => 'required',
            'receipt_method' => 'required|in:Wire Transfer,Cheque,Cash',

        ];
    }

    protected function other()
    {

        return [
            'receipt_amount' => 'required|integer',
            'receipt_amount_idr' => 'required|integer',
            'receipt_words' => 'required',
            // 'receipt_words_idr' => 'required',
            'receipt_method' => 'required|in:Wire Transfer,Cheque,Cash',

        ];
    }
}
