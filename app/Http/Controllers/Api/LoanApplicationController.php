<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateLoanApplication;
use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreLoanApplicationRequest;
use App\Http\Resources\LoanApplicationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class LoanApplicationController extends Controller
{
    public function store(StoreLoanApplicationRequest $request, CreateLoanApplication $action): JsonResponse
    {
        $user = $request->user();

        if ($user->district_id === null) {
            throw ValidationException::withMessages([
                'district' => 'Akaun anda tiada daerah berdaftar. Sila hubungi pentadbir.',
            ]);
        }

        try {
            $application = $action->handle(
                $user,
                $request->normalizedItems(),
                $request->validated('start_date'),
                $request->validated('end_date'),
                $request->validated('purpose'),
            );
        } catch (InsufficientStockException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => ['items' => [$e->getMessage()]],
            ], 422);
        }

        return (new LoanApplicationResource($application))
            ->response()
            ->setStatusCode(201);
    }
}
