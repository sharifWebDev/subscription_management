<form><div class="row"><div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="payment_master_id">Payment Master Id</label><br><input type="text" name="payment_master_id" class="form-control @error('payment_master_id') is-invalid @enderror" value="{{ old('payment_master_id', $query->payment_master_id ?? "") }}" placeholder="Enter Payment Master Id..." id="view_payment_master_id" disabled>@error('payment_master_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="payment_child_id">Payment Child Id</label><br><input type="text" name="payment_child_id" class="form-control @error('payment_child_id') is-invalid @enderror" value="{{ old('payment_child_id', $query->payment_child_id ?? "") }}" placeholder="Enter Payment Child Id..." id="view_payment_child_id" disabled>@error('payment_child_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="payment_transaction_id">Payment Transaction Id</label><br><input type="text" name="payment_transaction_id" class="form-control @error('payment_transaction_id') is-invalid @enderror" value="{{ old('payment_transaction_id', $query->payment_transaction_id ?? "") }}" placeholder="Enter Payment Transaction Id..." id="view_payment_transaction_id" disabled>@error('payment_transaction_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="allocatable_type">Allocatable Type</label><br><input type="text" name="allocatable_type" class="form-control @error('allocatable_type') is-invalid @enderror" value="{{ old('allocatable_type', $query->allocatable_type ?? "") }}" placeholder="Enter Allocatable Type..." id="view_allocatable_type" disabled>@error('allocatable_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="allocatable_id">Allocatable Id</label><br><input type="text" name="allocatable_id" class="form-control @error('allocatable_id') is-invalid @enderror" value="{{ old('allocatable_id', $query->allocatable_id ?? "") }}" placeholder="Enter Allocatable Id..." id="view_allocatable_id" disabled>@error('allocatable_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="amount">Amount</label><br><input type="number" step="any" name="amount" min="0" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $query->amount ?? "") }}" placeholder="Enter Amount..." id="view_amount" disabled>@error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="base_amount">Base Amount</label><br><input type="number" step="any" name="base_amount" min="0" class="form-control @error('base_amount') is-invalid @enderror" value="{{ old('base_amount', $query->base_amount ?? "") }}" placeholder="Enter Base Amount..." id="view_base_amount" disabled>@error('base_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="exchange_rate">Exchange Rate</label><br><input type="number" step="any" name="exchange_rate" min="0" class="form-control @error('exchange_rate') is-invalid @enderror" value="{{ old('exchange_rate', $query->exchange_rate ?? "") }}" placeholder="Enter Exchange Rate..." id="view_exchange_rate" disabled>@error('exchange_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="currency">Currency</label><br><input type="text" name="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency', $query->currency ?? "") }}" placeholder="Enter Currency..." id="view_currency" disabled>@error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="allocation_reference">Allocation Reference</label><br><input type="text" name="allocation_reference" class="form-control @error('allocation_reference') is-invalid @enderror" value="{{ old('allocation_reference', $query->allocation_reference ?? "") }}" placeholder="Enter Allocation Reference..." id="view_allocation_reference" disabled>@error('allocation_reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="allocation_type">Allocation Type</label><br><input type="text" name="allocation_type" class="form-control @error('allocation_type') is-invalid @enderror" value="{{ old('allocation_type', $query->allocation_type ?? "") }}" placeholder="Enter Allocation Type..." id="view_allocation_type" disabled>@error('allocation_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="is_reversed">Is Reversed</label><br><input type="radio" name="is_reversed" id="view_is_reversed_yes" value="1" {{ old('is_reversed', $query->is_reversed ?? "") == 1 ? "checked" : "" }} disabled> 
                                           <label for="editis_reversed_yes" disabled>Is Reversed Yes </label>
                                           <input type="radio" name="is_reversed" id="view_is_reversed_no" value="0" {{ old('is_reversed', $query->is_reversed ?? "") == 0 ? "checked" : "" }} disabled> 
                                           <label for="editis_reversed_no" disabled>Is Reversed No </label>@error('is_reversed')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="reversed_at">Reversed At</label><br><input type="datetime-local" name="reversed_at" class="form-control @error('reversed_at') is-invalid @enderror" value="{{ old('reversed_at', $query->reversed_at ?? "") }}" placeholder="Enter Reversed At..." id="view_reversed_at" disabled>@error('reversed_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="reversal_id">Reversal Id</label><br><input type="text" name="reversal_id" class="form-control @error('reversal_id') is-invalid @enderror" value="{{ old('reversal_id', $query->reversal_id ?? "") }}" placeholder="Enter Reversal Id..." id="view_reversal_id" disabled>@error('reversal_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="metadata">Metadata</label><br><input type="text" name="metadata" class="form-control @error('metadata') is-invalid @enderror" value="{{ old('metadata', $query->metadata ?? "") }}" placeholder="Enter Metadata..." id="view_metadata" disabled>@error('metadata')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="notes">Notes</label><br><textarea name="notes" class="form-control @error('notes') is-invalid @enderror" placeholder="Enter Notes..." id="view_notes" disabled>{{ old('notes', $query->notes ?? "") }}</textarea>@error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div><div class="mb-3 text-right">
                              <a type="button" class="btn bg-danger text-white" href="{{ route('admin.payment-allocations.index') }}">Close</a>
                          </div>
                      </div>
                  </form>