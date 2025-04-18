<?php

namespace Modules\Technician\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\HttpResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Modules\Auth\Http\Requests\Register\BaseRegisterRequest;

class TechnicianRequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        $inUpdate = !preg_match("/.*technicians$/", $this->url());

        return BaseRegisterRequest::baseRules($inUpdate);
    }

     /**
     * @throws ValidationException
     */
    public function failedValidation(Validator $validator): void
    {
        $this->throwValidationException($validator);
    }
}
