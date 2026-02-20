<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\DiscountDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiscountRequest;
use App\Http\Requests\UpdateDiscountRequest;
use App\Http\Resources\DiscountResource;
use App\Models\Discount;
use App\Services\DiscountService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function __construct(
        protected DiscountService $discountService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $discounts = $this->discountService->getAllDiscounts($request);

            return success('Records retrieved successfully', DiscountResource::collection($discounts));

        } catch (Exception $e) {

            info('Error retrieved Discount!', [$e]);

            return error('Discounts retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreDiscountRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $discount = $this->discountService->storeDiscount($dto);

    //         return success('Records saved successfully', new DiscountResource($discount));

    //     } catch (Exception $e) {

    //         info('Discounts data insert failed!', [$e]);
    //         return error('Discounts insert failed!.');
    //     }
    // }

    public function store(StoreDiscountRequest $request): JsonResponse
    {
        try {

            $discount = $this->discountService->storeDiscount($request->validated());

            return success('Records saved successfully', new DiscountResource($discount));

        } catch (Exception $e) {

            info('Discounts data insert failed!', [$e]);

            return error('Discounts insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(Discount $discount) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new DiscountResource($discount));

    //     } catch (\Exception $e) {
    //         info('Discounts data showing failed!', [$e]);
    //         return error('Discounts retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $discount = $this->discountService->getDiscountById($id);

            if (! $discount) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new DiscountResource($discount));

        } catch (\Exception $e) {
            info('Discounts data showing failed!', [$e]);

            return error('Discounts retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdateDiscountRequest $request, Discount $discount): JsonResponse
    //   {
    //       try {

    //         $discount = $this->discountService->updateDiscount($discount->id, $request->validated());

    //         return success('Records updated successfully', new DiscountResource($discount));

    //       } catch (\Exception $e) {
    //           info('Discounts update failed!', [$e]);
    //           return error('Discounts update failed!.');
    //       }
    //   }

    public function update(UpdateDiscountRequest $request, int $id): JsonResponse
    {
        try {

            $discount = $this->discountService->getDiscountById($id);

            // $dto = new DiscountDto($request->validated());
            // $this->discountService->updateDiscount($discount->id, $dto->toArray());

            $this->discountService->updateDiscount($discount->id, $request->validated());

            return success('Records updated successfully', new DiscountResource($discount));

        } catch (\Exception $e) {
            info('Discounts update failed!', [$e]);

            return error('Discounts update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(Discount $discount): JsonResponse
    // {
    //     try {

    //         if (! $discount) {
    //             return response()->json(['message' => 'Discounts not found!'], 404);
    //         }

    //         $discount = $this->discountService->deleteDiscount($discount->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Discounts delete failed!', [$e]);
    //         return error('Discounts delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $discount = $this->discountService->getDiscountById($id);

            if (! $discount) {
                return response()->json(['message' => 'Discounts not found!'], 404);
            }

            $this->discountService->deleteDiscount($discount->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Discounts delete failed!', [$e]);

            return error('Discounts delete failed!.');
        }
    }
}
