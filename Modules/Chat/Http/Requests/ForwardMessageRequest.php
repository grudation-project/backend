<?php

namespace Modules\Chat\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ForwardMessageRequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        return [
            'target_conversation_id' => ValidationRuleHelper::foreignKeyRules(),
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
