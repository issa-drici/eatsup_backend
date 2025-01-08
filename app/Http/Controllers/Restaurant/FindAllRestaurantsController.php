<?php

namespace App\Http\Controllers\Restaurant;

use App\Application\Usecases\Restaurant\FindAllRestaurantsUsecase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FindAllRestaurantsController extends Controller
{
    public function __construct(
        private FindAllRestaurantsUsecase $findAllRestaurantsUsecase
    ) {
    }

    public function __invoke(Request $request)
    {
        $filters = [
            'name' => $request->query('name'),
            'address' => $request->query('address'),
            'phone' => $request->query('phone'),
            'owner_name' => $request->query('owner_name'),
            'owner_email' => $request->query('owner_email'),
            'owner_plan' => $request->query('owner_plan'),
        ];

        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 10);

        $result = $this->findAllRestaurantsUsecase->execute($filters, $page, $perPage);

        return response()->json([
            'message' => 'Restaurants retrieved successfully',
            'data' => $result['data'],
            'meta' => [
                'current_page' => $result['current_page'],
                'per_page' => $result['per_page'],
                'total' => $result['total'],
                'last_page' => $result['last_page']
            ]
        ]);
    }
} 