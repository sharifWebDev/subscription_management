<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\FeatureDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeatureRequest;
use App\Http\Requests\UpdateFeatureRequest;
use App\Http\Resources\FeatureResource;
use App\Models\Feature;
use App\Services\FeatureService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    public function __construct(
        protected FeatureService $featureService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $features = $this->featureService->getAllFeatures($request);

            return success('Records retrieved successfully', FeatureResource::collection($features));

        } catch (Exception $e) {

            info('Error retrieved Feature!', [$e]);

            return error('Features retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreFeatureRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $feature = $this->featureService->storeFeature($dto);

    //         return success('Records saved successfully', new FeatureResource($feature));

    //     } catch (Exception $e) {

    //         info('Features data insert failed!', [$e]);
    //         return error('Features insert failed!.');
    //     }
    // }

    public function store(StoreFeatureRequest $request): JsonResponse
    {
        try {

            $feature = $this->featureService->storeFeature($request->validated());

            return success('Records saved successfully', new FeatureResource($feature));

        } catch (Exception $e) {

            info('Features data insert failed!', [$e]);

            return error('Features insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(Feature $feature) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new FeatureResource($feature));

    //     } catch (\Exception $e) {
    //         info('Features data showing failed!', [$e]);
    //         return error('Features retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $feature = $this->featureService->getFeatureById($id);

            if (! $feature) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new FeatureResource($feature));

        } catch (\Exception $e) {
            info('Features data showing failed!', [$e]);

            return error('Features retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdateFeatureRequest $request, Feature $feature): JsonResponse
    //   {
    //       try {

    //         $feature = $this->featureService->updateFeature($feature->id, $request->validated());

    //         return success('Records updated successfully', new FeatureResource($feature));

    //       } catch (\Exception $e) {
    //           info('Features update failed!', [$e]);
    //           return error('Features update failed!.');
    //       }
    //   }

    public function update(UpdateFeatureRequest $request, int $id): JsonResponse
    {
        try {

            $feature = $this->featureService->getFeatureById($id);

            // $dto = new FeatureDto($request->validated());
            // $this->featureService->updateFeature($feature->id, $dto->toArray());

            $this->featureService->updateFeature($feature->id, $request->validated());

            return success('Records updated successfully', new FeatureResource($feature));

        } catch (\Exception $e) {
            info('Features update failed!', [$e]);

            return error('Features update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(Feature $feature): JsonResponse
    // {
    //     try {

    //         if (! $feature) {
    //             return response()->json(['message' => 'Features not found!'], 404);
    //         }

    //         $feature = $this->featureService->deleteFeature($feature->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Features delete failed!', [$e]);
    //         return error('Features delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $feature = $this->featureService->getFeatureById($id);

            if (! $feature) {
                return response()->json(['message' => 'Features not found!'], 404);
            }

            $this->featureService->deleteFeature($feature->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Features delete failed!', [$e]);

            return error('Features delete failed!.');
        }
    }
}
