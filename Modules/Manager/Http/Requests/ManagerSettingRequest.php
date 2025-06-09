<?php

namespace Modules\Manager\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;

class ManagerSettingRequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        return [
            'automatic_assignment' => ValidationRuleHelper::booleanRules([
                'required' => 'sometimes',
            ]),
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        $this->throwValidationException($validator);
    }
}
