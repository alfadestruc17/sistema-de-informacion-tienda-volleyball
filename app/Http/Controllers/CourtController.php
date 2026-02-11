<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Court\AvailabilityRequest;
use App\Http\Requests\Court\StoreCourtRequest;
use App\Services\CourtService;
use Illuminate\Http\JsonResponse;

class CourtController extends Controller
{
    public function __construct(
        private CourtService $courtService
    ) {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->courtService->getAll());
    }

    public function store(StoreCourtRequest $request): JsonResponse
    {
        $court = $this->courtService->create($request->validated());
        return response()->json($court, 201);
    }

    public function availability(AvailabilityRequest $request): JsonResponse
    {
        $courtId = $request->validated('court_id');
        $dateFrom = $request->validated('date_from');
        $dateTo = $request->validated('date_to');

        $availability = $this->courtService->getAvailability($courtId, $dateFrom, $dateTo);
        $courts = $this->courtService->getCourtsForAvailability($courtId);

        return response()->json([
            'availability' => $availability,
            'courts' => $courts,
        ]);
    }
}
