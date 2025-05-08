<?php

namespace Modules\Map\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Modules\TripAddress\Enums\TripAddressTypeEnum;

class TripAddressRequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        $inUpdate = $this->method() === 'PUT';

        return [
            'name' => ValidationRuleHelper::stringRules([
                'required' => $inUpdate ? 'sometimes' : 'required',
            ]),
            'type' => ValidationRuleHelper::enumRules(TripAddressTypeEnum::toArray(), [
                'required' => $inUpdate ? 'sometimes' : 'required',
            ]),
            'latitude' => ValidationRuleHelper::latitudeRules([
                'required' => ! $inUpdate ? 'required' : 'required_with:longitude',
            ]),
            'longitude' => ValidationRuleHelper::longitudeRules([
                'required' => ! $inUpdate ? 'required' : 'required_with:latitude',
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
