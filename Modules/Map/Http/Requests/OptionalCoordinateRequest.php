<?php

namespace Modules\Map\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class OptionalCoordinateRequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        return [
            'latitude' => ValidationRuleHelper::latitudeRules([
                'required' => 'required_with:longitude',
            ]),
            'longitude' => ValidationRuleHelper::longitudeRules([
                'required' => 'required_with:latitude',
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
