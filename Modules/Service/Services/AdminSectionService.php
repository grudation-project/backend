<?php

namespace Modules\Service\Services;

use App\Exceptions\ValidationErrorsException;
use Modules\Service\Models\Section;

class AdminSectionService
{
    public function index($serviceId)
    {
        return Section::query()->where('service_id', $serviceId)->latest()->searchable()->get();
    }

    public function show($serviceId, $id)
    {
        return Section::query()->where('service_id', $serviceId)->findOrFail($id);
    }

    public function store(array $data, $serviceId)
    {
        AdminServiceLogic::exists($serviceId);

        $this->assertUnique($data['name'], $serviceId);

        return Section::query()->where('service_id', $serviceId)->create($data + ['service_id' => $serviceId]);
    }

    public function update(array $data, $serviceId, $id)
    {
        AdminServiceLogic::exists($serviceId);

        $this->assertUnique($data['name'], $serviceId, $id);

        $section = Section::query()->where('service_id', $serviceId)->findOrFail($id);

        $section->update($data + ['service_id' => $serviceId]);

        return $section;
    }

    public function destroy($serviceId, $id)
    {
        Section::query()->where('service_id', $serviceId)->findOrFail($id)->delete();
    }

    private function assertUnique($name, $serviceId, $id = null)
    {
        $exists = Section::query()
            ->where('name', $name)
            ->where('service_id', $serviceId)
            ->when(!is_null($id), fn($q) => $q->where('id', '<>', $id))
            ->exists();

        if ($exists) {
            throw new ValidationErrorsException([
                'name' => translate_error_message('name', 'exists'),
            ]);
        }
    }

    public static function exists($id, string $errorKey = 'section_id')
    {
        $section = Section::query()->find($id);

        if(! $section)
        {
            throw new ValidationErrorsException([
                $errorKey => translate_error_message('section', 'not_exists'),
            ]);
        }

        return $section;
    }
}
