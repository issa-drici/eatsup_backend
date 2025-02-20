<?php

namespace App\Http\Controllers\Restaurant;

use App\Application\Usecases\Restaurant\GetRestaurantOnboardingStatusUsecase;
use App\Http\Controllers\Controller;

class GetRestaurantOnboardingStatusController extends Controller
{
    public function __construct(
        private GetRestaurantOnboardingStatusUsecase $getRestaurantOnboardingStatusUsecase
    ) {}

    public function __invoke(string $restaurantId)
    {
        $onboardingStatus = $this->getRestaurantOnboardingStatusUsecase->execute($restaurantId);

        return response()->json([
            'message' => 'Restaurant onboarding status retrieved successfully',
            'data' => $onboardingStatus
        ]);
    }
}
