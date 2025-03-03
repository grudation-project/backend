<?php

namespace Modules\Auth\Http\Requests\Register;

use App\Helpers\RequestHelper;
use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{
    use HttpResponse;

    public function prepareForValidation(): void
    {
        RequestHelper::formatPhoneNumber($this);
    }

    public function rules(): array
    {
        return self::baseRules();
    }

    public static function baseRules()
    {
        return [
            ...BaseRegisterRequest::baseRules(),
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        $this->throwValidationException($validator);
    }
}
