<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\InvoiceDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService,

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $invoices = $this->invoiceService->getAllInvoices($request);

            return success('Records retrieved successfully', InvoiceResource::collection($invoices));

        } catch (Exception $e) {

            info('Error retrieved Invoice!', [$e]);

            return error('Invoices retrieved failed!.');

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreInvoiceRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $invoice = $this->invoiceService->storeInvoice($dto);

    //         return success('Records saved successfully', new InvoiceResource($invoice));

    //     } catch (Exception $e) {

    //         info('Invoices data insert failed!', [$e]);
    //         return error('Invoices insert failed!.');
    //     }
    // }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        try {

            $invoice = $this->invoiceService->storeInvoice($request->validated());

            return success('Records saved successfully', new InvoiceResource($invoice));

        } catch (Exception $e) {

            info('Invoices data insert failed!', [$e]);

            return error('Invoices insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(Invoice $invoice) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new InvoiceResource($invoice));

    //     } catch (\Exception $e) {
    //         info('Invoices data showing failed!', [$e]);
    //         return error('Invoices retrieved failed!.');
    //     }
    // }

    public function find(int $id): JsonResponse
    {
        try {

            $invoice = $this->invoiceService->getInvoiceById($id);

            if (! $invoice) {
                throw new ModelNotFoundException;
            }

            return success('Records retrieved successfully', new InvoiceResource($invoice));

        } catch (\Exception $e) {
            info('Invoices data showing failed!', [$e]);

            return error('Invoices retrieval failed!');
        }
    }

    //   /**
    //    * Update the specified resource in storage.
    //    */
    //   public function update(UpdateInvoiceRequest $request, Invoice $invoice): JsonResponse
    //   {
    //       try {

    //         $invoice = $this->invoiceService->updateInvoice($invoice->id, $request->validated());

    //         return success('Records updated successfully', new InvoiceResource($invoice));

    //       } catch (\Exception $e) {
    //           info('Invoices update failed!', [$e]);
    //           return error('Invoices update failed!.');
    //       }
    //   }

    public function update(UpdateInvoiceRequest $request, int $id): JsonResponse
    {
        try {

            $invoice = $this->invoiceService->getInvoiceById($id);

            // $dto = new InvoiceDto($request->validated());
            // $this->invoiceService->updateInvoice($invoice->id, $dto->toArray());

            $this->invoiceService->updateInvoice($invoice->id, $request->validated());

            return success('Records updated successfully', new InvoiceResource($invoice));

        } catch (\Exception $e) {
            info('Invoices update failed!', [$e]);

            return error('Invoices update failed!.');
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(Invoice $invoice): JsonResponse
    // {
    //     try {

    //         if (! $invoice) {
    //             return response()->json(['message' => 'Invoices not found!'], 404);
    //         }

    //         $invoice = $this->invoiceService->deleteInvoice($invoice->id);
    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Invoices delete failed!', [$e]);
    //         return error('Invoices delete failed!.');
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {

            $invoice = $this->invoiceService->getInvoiceById($id);

            if (! $invoice) {
                return response()->json(['message' => 'Invoices not found!'], 404);
            }

            $this->invoiceService->deleteInvoice($invoice->id);

            return success('Records deleted successfully');

        } catch (\Exception $e) {
            info('Invoices delete failed!', [$e]);

            return error('Invoices delete failed!.');
        }
    }
}
