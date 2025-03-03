<?php

namespace Modules\Chat\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ConversationPinRequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        return [
            'pin' => ValidationRuleHelper::booleanRules(),
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
