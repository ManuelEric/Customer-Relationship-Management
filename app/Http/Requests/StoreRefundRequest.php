<?php

namespace App\Http\Requests;

use App\Interfaces\ClientProgramRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;

class StoreRefundRequest extends FormRequest
{

    private ClientProgramRepositoryInterface $clientProgramRepository;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function __construct(ClientProgramRepositoryInterface $clientProgramRepository)
    {
        $this->clientProgramRepository = $clientProgramRepository;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        $clientprog_id = $this->route('client_program');
        $clientProg = $this->clientProgramRepository->getClientProgramById($clientprog_id);
        $total_payment = $clientProg->invoice->inv_totalprice_idr;
        $total_paid = $clientProg->invoice->receipt()->sum('receipt_amount_idr');

        return [
            'total_payment' => 'required|integer|in:' . $total_payment,
            'total_paid' => 'required|integer|in:' . $total_paid,
            'percentage_refund' => 'required|numeric|min:0',
            'refund_amount' => 'required|numeric|min:0',
            'tax_percentage' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'total_refunded' => 'required|lte:total_paid|numeric|min:1',
        ];
    }
}
