<?php

namespace Modules\Auth\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ChangePasswordRequest extends FormRequest
{
    use HttpResponse;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'old_password' => [
                'required',
                'current_password',
            ],
            'new_password' => ValidationRuleHelper::defaultPasswordRules(),
        ];
    }

    public function messages(): array
    {
        return [
            'old_password.required' => translate_error_message('old_password', 'required'),
            'new_password.required' => translate_error_message('new_password', 'required'),
        ];
    }

    /**
     * @throws ValidationException
     */
    public function failedValidation(Validator $validator): void
    {
        $this->throwValidationException($validator);
    }
}
