<?php

namespace Modules\FcmNotification\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Auth\Enums\UserTypeEnum;

class NotifyUserRequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        return [
            'target' => ValidationRuleHelper::enumRules([UserTypeEnum::VENDOR, UserTypeEnum::CLIENT, 3]),
            'title' => ValidationRuleHelper::stringRules(),
            'body' => ValidationRuleHelper::stringRules(),
            'image' => ValidationRuleHelper::stringRules([
                'required' => 'nullable',
            ]),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $this->throwValidationException($validator);
    }
}
