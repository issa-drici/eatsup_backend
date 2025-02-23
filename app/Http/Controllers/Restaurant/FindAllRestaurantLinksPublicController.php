<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Application\Usecases\Restaurant\FindAllRestaurantLinksPublicUsecase;

class FindAllRestaurantLinksPublicController extends Controller
{
    public function __construct(
        private FindAllRestaurantLinksPublicUsecase $findAllRestaurantLinksPublicUsecase
    ) {
    }

    public function __invoke()
    {
        $links = $this->findAllRestaurantLinksPublicUsecase->execute();

        return response()->json([
            'data' => $links
        ]);
    }
}
