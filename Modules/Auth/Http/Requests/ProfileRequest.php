<?php

namespace Modules\Auth\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ProfileRequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        return [
            'name' => ValidationRuleHelper::stringRules(['required' => 'sometimes']),
            'phone' => ValidationRuleHelper::phoneRules(['required' => 'nullable']),
            'email' => ValidationRuleHelper::emailRules(['required' => 'sometimes']),
            'avatar' => ValidationRuleHelper::storeOrUpdateImageRules(true),
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
