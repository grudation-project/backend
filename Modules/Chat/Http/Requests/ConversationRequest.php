<?php

namespace Modules\Chat\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Modules\Chat\Enums\ConversationTypeEnum;

class ConversationRequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        return [
            'type' => ValidationRuleHelper::enumRules(ConversationTypeEnum::toArray()),
            'user_id' => ValidationRuleHelper::foreignKeyRules([
                'required' => $this->type == ConversationTypeEnum::PRIVATE ? 'required' : 'exclude',
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
