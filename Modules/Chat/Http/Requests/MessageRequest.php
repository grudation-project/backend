<?php

namespace Modules\Chat\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Modules\Chat\Enums\MessageTypeEnum;

class MessageRequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        $rules = [
            'type' => ValidationRuleHelper::enumRules(MessageTypeEnum::toArray()),
            'content' => ValidationRuleHelper::longTextRules([
                'required' => 'nullable',
            ]),
            'parent_id' => ValidationRuleHelper::foreignKeyRules([
                'required' => 'sometimes',
            ]),
        ];

        switch ($this->type) {
            case MessageTypeEnum::IMAGE:
            case MessageTypeEnum::AUDIO:
            case MessageTypeEnum::VIDEO:
            case MessageTypeEnum::DOCUMENT:
            case MessageTypeEnum::RECORD:
                $rules['media'] = ValidationRuleHelper::fileRules(false, [
                    'mimes' => 'mimes:'.$this->getMimes(),
                ]);

                if ($this->type == MessageTypeEnum::RECORD) {
                    $rules['record_duration'] = ValidationRuleHelper::integerRules();
                    unset($rules['content']);
                }

                break;

            case MessageTypeEnum::LOCATION:
                $rules['media'] = ValidationRuleHelper::fileRules(false, [
                    'mimes' => 'mimes:'.$this->getMimes(),
                ]);
                $rules['location'] = ValidationRuleHelper::arrayRules();
                $rules['location.latitude'] = ValidationRuleHelper::latitudeRules();
                $rules['location.longitude'] = ValidationRuleHelper::longitudeRules();
                break;
            default:
                $rules['content'] = ValidationRuleHelper::longTextRules();
                break;
        }

        return $rules;
    }

    /**
     * @throws ValidationException
     */
    public function failedValidation(Validator $validator): void
    {
        $this->throwValidationException($validator);
    }

    public function getMimes(): string
    {
        return match ((int) $this->type) {
            MessageTypeEnum::DOCUMENT => 'txt,pdf',
            MessageTypeEnum::LOCATION => 'png,jpg',
            MessageTypeEnum::IMAGE => 'png,jpg,gif',
            MessageTypeEnum::RECORD, MessageTypeEnum::AUDIO => 'mp3,m4a,webm,3gp,mp4',
            MessageTypeEnum::VIDEO => 'mp4',
            default => 'test',
        };
    }
}
