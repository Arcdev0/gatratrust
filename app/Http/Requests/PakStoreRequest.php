<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PakStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_name' => 'required|string|max:255',
            'project_number' => 'required|string|max:100',
            'project_value' => 'required|numeric|min:0',
            'location_project' => 'required|string|max:255',
            'date' => 'required|date',
            'customer_name' => 'required|string|max:255',
            'customer_address' => 'nullable|string',
            'attention' => 'nullable|string|max:255',
            'your_reference' => 'nullable|string|max:255',
            'terms_text' => 'nullable|string',
            'employee' => 'required|array|min:1',
            'employee.*' => 'exists:karyawan_data,id',
            'items' => 'required|array|min:1',
            'items.*' => 'required|array|min:1',
            'items.*.*.operational_needs' => 'required|string|max:255',
            'items.*.*.description' => 'nullable|string',
            'items.*.*.qty' => 'required|numeric|min:0',
            'items.*.*.unit_cost' => 'required|numeric|min:0',
            'items.*.*.total_cost' => 'required|numeric|min:0',
            'items.*.*.max_cost' => 'nullable|numeric|min:0',
            'items.*.*.percent' => 'nullable',
            'items.*.*.status' => 'nullable|string',
            'scopes_master' => 'nullable|array',
            'scopes_master.*.description' => 'required_with:scopes_master|string|max:255',
            'scopes_master.*.responsible_pt_gpt' => 'nullable|boolean',
            'scopes_master.*.responsible_client' => 'nullable|boolean',
            'terms_master' => 'nullable|array',
            'terms_master.*.description' => 'required_with:terms_master|string',
            'pph23' => 'nullable|numeric|min:0',
            'ppn11' => 'nullable|numeric|min:0',
            'project_cost' => 'nullable|numeric|min:0',
        ];
    }
}
