<?php

namespace App\Http\Requests;

use App\Api\ApiError;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class TriggerEmailToConfirmRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(ApiError::validate(
            'Houve um ou mais erros nos paramêtros enviados!',
            $validator->errors()
        ));
    }
}
