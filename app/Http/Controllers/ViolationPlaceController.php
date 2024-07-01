<?php

namespace App\Http\Controllers;

use App\Http\Requests\Violation\ViolationRequest;
use App\Models\ViolationPlace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ViolationPlaceController extends Controller
{
    public function store(ViolationRequest $request)
    {
        $data = $request->validated();
        $violation = ViolationPlace::create($data);

        return response()->json($violation, 201);
    }

    public function index(Request $request)
    {
        DB::enableQueryLog(); // Включение логирования запросов

        $perPage = $request->query('per_page', 40); // Количество элементов на страницу по умолчанию

        $query = ViolationPlace::query();

        // Проверка наличия параметра parent_id
        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->query('parent_id'));
        }

        // Фильтрация по имени
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Фильтрация по активности
        if ($request->has('filter_active') && $request->filter_active !== '') {
            $query->where('is_active', $request->filter_active == 'true');
        }

        // Фильтрация по выбираемости
        if ($request->has('filter_selectable') && $request->filter_selectable !== '') {
            $query->where('is_selectable', $request->filter_selectable == 'true');
        }

        // Сортировка
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'children_count_asc':
                    $query->withCount('children')->orderBy('children_count', 'asc');
                    break;
                case 'children_count_desc':
                    $query->withCount('children')->orderBy('children_count', 'desc');
                    break;
            }
        }

        // Пагинация результата
        $violations = $query->paginate($perPage);

        // Логирование выполненного запроса
        Log::info(DB::getQueryLog());

        return response()->json($violations);
    }



    public function update(ViolationRequest $request, ViolationPlace $violation)
    {
        $data = $request->validated();
        $violation->update($data);

        return response()->json($violation);
    }

    public function destroy(ViolationPlace $violation)
    {
        $violation->delete();

        return response()->json(null, 204);
    }
}
