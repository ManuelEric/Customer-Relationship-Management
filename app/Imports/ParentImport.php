<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ParentImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        //
    }

    public function rules(): array
    {
        return [
            '*.first_name' => ['required'],
            '*.last_name' => ['nullable'],
            '*.email' => ['required', 'email', 'unique:tbl_client,mail'],
            '*.phone_number' => ['required', 'min:10', 'max:15'],
            '*.date_of_birth' => ['required'],
            '*.instagram' => ['nullable', 'unique:tbl_client,insta'],
            '*.state' => ['required'],
            '*.city' => ['nullable'],
            '*.postal_code' => ['nullable'],
            '*.address' => ['nullable'],
            '*.lead_source' => ['required'],
            '*.lead_source' => ['required'],
            '*.level_of_interest' => ['required', 'in:High,Medium,Low'],
            '*.interest_program' => ['sometimes', 'required', 'exists:tbl_prog.prog_id'],
            '*.children_name' => ['nullable'],
        ];
    }
}
