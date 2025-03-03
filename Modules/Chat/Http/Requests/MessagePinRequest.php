<?php

namespace Modules\Chat\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class MessagePinRequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        return [
            'pin' => ValidationRuleHelper::booleanRules(),

            // in days
            'pinned_till' => ValidationRuleHelper::enumRules([1, 7, 30], [
                'required' => $this->pin ? 'required' : 'exclude',
            ]),
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
