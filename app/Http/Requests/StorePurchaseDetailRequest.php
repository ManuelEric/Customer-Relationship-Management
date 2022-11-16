<?php

namespace App\Http\Requests;

use App\Models\PurchaseDetail;
use App\Models\PurchaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseDetailRequest extends FormRequest
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
            'item' => 'required',
            'amount' => 'required|min:1|integer',
            'price_per_unit' => 'required|integer',
            'total' => 'required',
        ];
    }

    protected function update()
    {
        $purchaseId = $this->route('purchase');
        $detailId = $this->route('detail');

        return [
            'purchase_id' => [
                function ($attribute, $value, $fail) use ($purchaseId) {
                    if (!PurchaseRequest::where('purchase_id', $purchaseId)->first())
                        $fail('Purchase request cannot be found');
                }
            ],
            'id' => [
                function ($attribute, $value, $fail) use ($detailId) {
                    if (!PurchaseDetail::find($detailId))
                        $fail('Requested item cannot be found');
                }
            ],
            'item' => 'required',
            'amount' => 'required|min:1|integer',
            'price_per_unit' => 'required|integer',
            'total' => 'required',
        ];
    }
}
