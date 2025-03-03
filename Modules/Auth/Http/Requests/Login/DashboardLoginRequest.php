<?php

namespace Modules\Auth\Http\Requests\Login;

use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class DashboardLoginRequest extends FormRequest
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

    /**
     * @throws ValidationException
     */
    public function failedValidation(Validator $validator): void
    {
        $this->throwValidationException($validator);
    }
}
