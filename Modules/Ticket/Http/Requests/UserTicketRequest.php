<?php

namespace Modules\Ticket\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\HttpResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class UserTicketRequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        $inUpdate = $this->method() == 'PUT';

        return [
            'title' => ValidationRuleHelper::stringRules([
                $inUpdate ? 'sometimes' : 'required',
            ]),
            'description' => ValidationRuleHelper::longTextRules([
                $inUpdate ? 'sometimes' : 'required',
            ]),
            'section_id' => ValidationRuleHelper::foreignKeyRules([
                $inUpdate ? 'sometimes' : 'required',
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
