<form><div class="row"><div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="subscription_id">Subscription Id</label><br><input type="text" name="subscription_id" class="form-control @error('subscription_id') is-invalid @enderror" value="{{ old('subscription_id', $query->subscription_id ?? "") }}" placeholder="Enter Subscription Id..." id="view_subscription_id" disabled>@error('subscription_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="feature_id">Feature Id</label><br><input type="text" name="feature_id" class="form-control @error('feature_id') is-invalid @enderror" value="{{ old('feature_id', $query->feature_id ?? "") }}" placeholder="Enter Feature Id..." id="view_feature_id" disabled>@error('feature_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="aggregate_date">Aggregate Date</label><br><input type="date" name="aggregate_date" class="form-control @error('aggregate_date') is-invalid @enderror" value="{{ old('aggregate_date', $query->aggregate_date ?? "") }}" placeholder="Enter Aggregate Date..." id="view_aggregate_date" disabled>@error('aggregate_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="aggregate_period">Aggregate Period</label><br><input type="text" name="aggregate_period" class="form-control @error('aggregate_period') is-invalid @enderror" value="{{ old('aggregate_period', $query->aggregate_period ?? "") }}" placeholder="Enter Aggregate Period..." id="view_aggregate_period" disabled>@error('aggregate_period')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="total_quantity">Total Quantity</label><br><input type="number" step="any" name="total_quantity" min="0" class="form-control @error('total_quantity') is-invalid @enderror" value="{{ old('total_quantity', $query->total_quantity ?? "") }}" placeholder="Enter Total Quantity..." id="view_total_quantity" disabled>@error('total_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="tier1_quantity">Tier1 Quantity</label><br><input type="number" step="any" name="tier1_quantity" min="0" class="form-control @error('tier1_quantity') is-invalid @enderror" value="{{ old('tier1_quantity', $query->tier1_quantity ?? "") }}" placeholder="Enter Tier1 Quantity..." id="view_tier1_quantity" disabled>@error('tier1_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="tier2_quantity">Tier2 Quantity</label><br><input type="number" step="any" name="tier2_quantity" min="0" class="form-control @error('tier2_quantity') is-invalid @enderror" value="{{ old('tier2_quantity', $query->tier2_quantity ?? "") }}" placeholder="Enter Tier2 Quantity..." id="view_tier2_quantity" disabled>@error('tier2_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="tier3_quantity">Tier3 Quantity</label><br><input type="number" step="any" name="tier3_quantity" min="0" class="form-control @error('tier3_quantity') is-invalid @enderror" value="{{ old('tier3_quantity', $query->tier3_quantity ?? "") }}" placeholder="Enter Tier3 Quantity..." id="view_tier3_quantity" disabled>@error('tier3_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="total_amount">Total Amount</label><br><input type="number" step="any" name="total_amount" min="0" class="form-control @error('total_amount') is-invalid @enderror" value="{{ old('total_amount', $query->total_amount ?? "") }}" placeholder="Enter Total Amount..." id="view_total_amount" disabled>@error('total_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="record_count">Record Count</label><br><input type="number" name="record_count" min="0" class="form-control @error('record_count') is-invalid @enderror" value="{{ old('record_count', $query->record_count ?? "") }}" placeholder="Enter Record Count..." id="view_record_count" disabled>@error('record_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="last_calculated_at">Last Calculated At</label><br><input type="datetime-local" name="last_calculated_at" class="form-control @error('last_calculated_at') is-invalid @enderror" value="{{ old('last_calculated_at', $query->last_calculated_at ?? "") }}" placeholder="Enter Last Calculated At..." id="view_last_calculated_at" disabled>@error('last_calculated_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div><div class="mb-3 text-right">
                              <a type="button" class="btn bg-danger text-white" href="{{ route('admin.metered-usage-aggregates.index') }}">Close</a>
                          </div>
                      </div>
                  </form>