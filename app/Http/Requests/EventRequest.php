<?php

namespace App\Http\Requests;

use App\Http\Traits\HasValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    use HasValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->eventRules();
    }
}
