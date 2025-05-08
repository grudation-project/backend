<?php

namespace Modules\Markable\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Comment\Models\Comment;
use Modules\Markable\Helpers\FavoriteHelper;
use Modules\Markable\Http\Requests\FavoriteToggleRequest;

class FavoriteController extends Controller
{
    use HttpResponse;

    public static array $allowedTypes = [
        'comment' => Comment::class,
    ];

    public static array $hasStatus = [];

    public function __invoke(FavoriteToggleRequest $request)
    {
        $modelType = $request->input('model_type');
        $modelID = $request->input('model_id');
        $errors = [];

        $modelObject = (static::$allowedTypes[$modelType])::query()
            ->whereId($modelID)
            ->when(static::$hasStatus[$modelType] ?? false, fn ($query) => $query->whereStatus(true))
            ->firstOr(function () use (&$errors) {
                $errors['model_id'] = translate_error_message('model', 'not_exists');
            });

        if ($modelObject) {
            DB::transaction(function () use ($modelObject, $modelType) {
                $favoriteExists = FavoriteHelper::model()::has($modelObject, auth()->user(), null);
                $favoriteExists
                    ? FavoriteHelper::model()::remove($modelObject, auth()->user(), null)
                    : FavoriteHelper::model()::add($modelObject, auth()->user(), null);
            });

            return $this->okResponse(
                message: translate_success_message('model', 'toggled')
            );
        }

        return $this->validationErrorsResponse($errors);
    }
}
