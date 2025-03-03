<?php

namespace Modules\Auth\Http\Requests;

use App\Helpers\RequestHelper;
use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CodeSendRequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        return [
            'handle' => ValidationRuleHelper::emailRules(),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $this->throwValidationException($validator);
    }
}
