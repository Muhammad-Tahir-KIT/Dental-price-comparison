<?php


namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="PriceComparisonRequest",
 *     title="Price Comparison Request",
 *     required={"items"},
 *     @OA\Property(
 *         property="items",
 *         type="array",
 *         description="An array of items to be priced.",
 *         @OA\Items(
 *             type="object",
 *             required={"product_name", "quantity"},
 *             @OA\Property(property="product_name", type="string", example="Dental Floss"),
 *             @OA\Property(property="quantity", type="integer", minimum=1, example=5)
 *         )
 *     )
 * )
 */
class PriceComparisonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_name' => ['required', 'string'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
