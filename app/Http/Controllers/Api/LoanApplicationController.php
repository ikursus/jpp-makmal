<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateLoanApplication;
use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreLoanApplicationRequest;
use App\Http\Resources\LoanApplicationResource;
use Illuminate\Http\JsonResponse;

class LoanApplicationController extends Controller
{
    public function store(StoreLoanApplicationRequest $request, CreateLoanApplication $action): JsonResponse
    {
        try {
            $application = $action->handle(
                $request->user(),
                $request->normalizedItems(),
                $request->validated('start_date'),
                $request->validated('end_date'),
                $request->validated('purpose'),
            );
        } catch (InsufficientStockException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new LoanApplicationResource($application))
            ->response()
            ->setStatusCode(201);
    }
}
