<?php

namespace Modules\Auth\Http\Requests\Login;

use App\Helpers\RequestHelper;
use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Modules\Auth\Helpers\UserTypeHelper;

class LoginRequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        return [
            'email' => ValidationRuleHelper::emailRules(),
            'password' => 'required',
            'fcm_token' => ['sometimes', 'string'],
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        $this->throwValidationException($validator);
    }
}
