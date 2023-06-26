<?php

namespace App\Http\Requests;

use App\Api\ApiError;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRegisterRequest extends FormRequest
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
        if (is_null($this->type)) {
            return [
                'type' => 'required|in:' . implode(',', User::getTypes())
            ];
        }

        if ($this->type == User::TYPE_ADMIN) {
            return [
                'type' => 'required|in:' . implode(',', User::getTypes()),
                'name' => 'required|max:200',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed'
            ];
        }

        if ($this->type == User::TYPE_MANAGER) {
            return [
                'type' => 'required|in:' . implode(',', User::getTypes()),
                'name' => 'required|max:200',
                'email' => 'required|email|unique:users,email',
                'phone' => 'nullable|min:13|max:13',
                'password' => 'required|confirmed'
            ];
        }

        if ($this->type == User::TYPE_DEPENDENT) {
            return [
                'type' => 'required|in:' . implode(',', User::getTypes()),
                'manager_id' => 'required|numeric',
                'name' => 'required|max:200',
                'username' => 'required|unique:users,username',
                'kinship' => 'required',
                'password' => 'required|confirmed'
            ];
        }
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(ApiError::validate(
            'Houve um ou mais erros nos paramêtros enviados!',
            $validator->errors()
        ));
    }
}
