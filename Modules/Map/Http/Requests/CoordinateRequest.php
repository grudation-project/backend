<?php

namespace Modules\Map\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CoordinateRequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        return [
            'latitude' => ValidationRuleHelper::latitudeRules(),
            'longitude' => ValidationRuleHelper::longitudeRules(),
            'address' => ValidationRuleHelper::stringRules([
                'required' => 'nullable',
            ]),
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        $this->throwValidationException($validator);
    }
}
