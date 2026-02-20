<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\HkProdUomDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHkProdUomRequest;
use App\Http\Requests\UpdateHkProdUomRequest;
use App\Http\Resources\HkProdUomResource;
use App\Models\HkProdUom;
use App\Services\HkProdUomService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HkProdUomController extends Controller
{
    public function __construct(
        protected HkProdUomService $hkProdUomService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $hkProdUoms = $this->hkProdUomService->getAllHkProdUoms($request);

            return success('Records retrieved successfully', HkProdUomResource::collection($hkProdUoms));

        } catch (Exception $e) {

            info('Error retrieved HkProdUom!', [$e]);

            return error('Hk Prod Uoms retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreHkProdUomRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $hkProdUom = $this->hkProdUomService->storeHkProdUom($dto);

    //         return success('Records saved successfully', new HkProdUomResource($hkProdUom));

    //     } catch (Exception $e) {

    //         info('Hk Prod Uoms data insert failed!', [$e]);
    //         return error('Hk Prod Uoms insert failed!.');
    //     }
    // }

    public function store(StoreHkProdUomRequest $request): JsonResponse
    {
        try {

            $hkProdUom = $this->hkProdUomService->storeHkProdUom($request->validated());

            return success('Records saved successfully', new HkProdUomResource($hkProdUom));

        } catch (Exception $e) {

            info('Hk Prod Uoms data insert failed!', [$e]);

            return error('Hk Prod Uoms insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(HkProdUom $hk_prod_uom) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new HkProdUomResource($hk_prod_uom));

    //     } catch (\Exception $e) {
    //         info('Hk Prod Uoms data showing failed!', [$e]);
    //         return error('Hk Prod Uoms retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $hk_prod_uom = $this->hkProdUomService->getHkProdUomById($id);

            if (! $hk_prod_uom) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new HkProdUomResource($hk_prod_uom));

        } catch (\Exception $e) {
            info('Hk Prod Uoms data showing failed!', [$e]);

            return error('Hk Prod Uoms retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdateHkProdUomRequest $request, HkProdUom $hk_prod_uom): JsonResponse
    //   {
    //       try {

    //         $hkProdUom = $this->hkProdUomService->updateHkProdUom($hk_prod_uom->id, $request->validated());

    //         return success('Records updated successfully', new HkProdUomResource($hkProdUom));

    //       } catch (\Exception $e) {
    //           info('Hk Prod Uoms update failed!', [$e]);
    //           return error('Hk Prod Uoms update failed!.');
    //       }
    //   }

    public function update(UpdateHkProdUomRequest $request, int $id): JsonResponse
    {
        try {

            $hkProdUom = $this->hkProdUomService->getHkProdUomById($id);

            // $dto = new HkProdUomDto($request->validated());
            // $this->hkProdUomService->updateHkProdUom($hkProdUom->id, $dto->toArray());

            $this->hkProdUomService->updateHkProdUom($hkProdUom->id, $request->validated());

            return success('Records updated successfully', new HkProdUomResource($hkProdUom));

        } catch (\Exception $e) {
            info('Hk Prod Uoms update failed!', [$e]);

            return error('Hk Prod Uoms update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(HkProdUom $hk_prod_uom): JsonResponse
    // {
    //     try {

    //         if (! $hk_prod_uom) {
    //             return response()->json(['message' => 'Hk Prod Uoms not found!'], 404);
    //         }

    //         $hkProdUom = $this->hkProdUomService->deleteHkProdUom($hk_prod_uom->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Hk Prod Uoms delete failed!', [$e]);
    //         return error('Hk Prod Uoms delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $hkProdUom = $this->hkProdUomService->getHkProdUomById($id);

            if (! $hkProdUom) {
                return response()->json(['message' => 'Hk Prod Uoms not found!'], 404);
            }

            $this->hkProdUomService->deleteHkProdUom($hkProdUom->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Hk Prod Uoms delete failed!', [$e]);

            return error('Hk Prod Uoms delete failed!.');
        }
    }
}
