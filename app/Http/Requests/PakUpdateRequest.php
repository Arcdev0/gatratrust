<?php

namespace App\Http\Requests;

class PakUpdateRequest extends PakStoreRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['project_number'] = 'required|string|max:100';

        return $rules;
    }
}
