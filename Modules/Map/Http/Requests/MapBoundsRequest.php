<?php

namespace Modules\Map\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class MapBoundsRequest extends FormRequest
{
    use HttpResponse;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'bounds_north_east_latitude' => ValidationRuleHelper::latitudeRules(),
            'bounds_north_east_longitude' => ValidationRuleHelper::longitudeRules(),
            'bounds_south_west_latitude' => ValidationRuleHelper::latitudeRules(),
            'bounds_south_west_longitude' => ValidationRuleHelper::longitudeRules(),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $this->throwValidationException($validator);
    }
}
