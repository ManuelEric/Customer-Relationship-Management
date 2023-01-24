<?php

namespace App\Http\Requests;

use App\Interfaces\SchoolProgramRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;

class StoreRefundSchoolRequest extends FormRequest
{

    private SchoolProgramRepositoryInterface $schoolProgramRepository;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function __construct(SchoolProgramRepositoryInterface $schoolProgramRepository)
    {
        $this->schoolProgramRepository = $schoolProgramRepository;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        return [
            'total_price' => 'required',
            'total_payment' => 'required',
            'percentage_payment' => 'nullable|numeric|max:100',
            'refunded_amount' => 'required',
            'refunded_tax_percentage' => 'nullable|numeric|max:100',
            'refunded_tax_amount' => 'nullable',
            'total_refunded' => 'required|lte:total_paid',

        ];
    }
}
