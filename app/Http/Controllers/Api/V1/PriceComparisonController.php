<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\NoSupplierFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\PriceComparisonRequest;
use App\Services\PriceComparisonService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *     title="Dental Price Comparison API",
 *     version="1.0.0"
 * )
 */
class PriceComparisonController extends Controller
{
    protected PriceComparisonService $priceComparisonService;

    public function __construct(PriceComparisonService $priceComparisonService)
    {
        $this->priceComparisonService = $priceComparisonService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/price-comparison",
     *     summary="Find the cheapest supplier for a list of products",
     *     tags={"Price Comparison"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PriceComparisonRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful comparison",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="supplier", type="string", example="Supplier B"),
     *             @OA\Property(property="total_price", type="number", format="float", example=102.00),
     *             @OA\Property(
     *                  property="breakdown",
     *                  type="object",
     *                  example={
     *                      "Dental Floss": {{"packages_count": 5, "package_size": 1, "package_price": 8.00}},
     *                      "Ibuprofen": {{"packages_count": 2, "package_size": 5, "package_price": 25.00}, {"packages_count": 2, "package_size": 1, "package_price": 6.00}}
     *                  }
     *              )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No supplier can fulfill the order",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No supplier could be found to fulfill the entire order.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function compare(PriceComparisonRequest $request): JsonResponse
    {
        $orderItems = $request->validated()['items'];

        $result = $this->priceComparisonService->findBestPrice($orderItems);
        if (!$result) {
            return response()->json([
                'message' => 'No supplier could be found to fulfill the entire order.'
            ], 404);
        }

        return response()->json($result);
    }
}
