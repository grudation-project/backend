<?php

namespace Modules\Service\Services;

use App\Exceptions\ValidationErrorsException;
use Modules\Service\Models\Service;

class AdminServiceLogic
{
    public function index()
    {
        return Service::query()
            ->latest()
            ->searchable()
            ->get();
    }

    public function show($id)
    {
        return Service::query()->findOrFail($id);
    }

    public function store(array $data)
    {
        $this->assertUnique($data['name']);

        $service = Service::query()->create($data);

        return $this->show($service->id);
    }

    public function update(array $data, $id)
    {
        $service = Service::query()->findOrFail($id);

        if(isset($data['name'])) {
            $this->assertUnique($data['name'], $service->id);
        }

        $service->update($data);

        return $this->show($service->id);
    }

    public function destroy($id)
    {
        Service::query()->findOrFail($id)->delete();
    }

    private function assertUnique(string $name, $ignoredId = null)
    {
        $exists = Service::query()
            ->where('name', $name)
            ->when(! is_null($ignoredId), fn($q) => $q->where('id', '<>', $ignoredId))
            ->exists();

        if($exists) {
            throw new ValidationErrorsException([
                'name' => translate_error_message('name', 'exists'),
            ]);
        }
    }
}
