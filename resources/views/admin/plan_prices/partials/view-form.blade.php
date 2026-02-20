<form><div class="row"><div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="plan_id">Plan Id</label><br><input type="text" name="plan_id" class="form-control @error('plan_id') is-invalid @enderror" value="{{ old('plan_id', $query->plan_id ?? "") }}" placeholder="Enter Plan Id..." id="view_plan_id" disabled>@error('plan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="currency">Currency</label><br><input type="text" name="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency', $query->currency ?? "") }}" placeholder="Enter Currency..." id="view_currency" disabled>@error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="amount">Amount</label><br><input type="number" step="any" name="amount" min="0" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $query->amount ?? "") }}" placeholder="Enter Amount..." id="view_amount" disabled>@error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="interval">Interval</label><br><input type="text" name="interval" class="form-control @error('interval') is-invalid @enderror" value="{{ old('interval', $query->interval ?? "") }}" placeholder="Enter Interval..." id="view_interval" disabled>@error('interval')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="interval_count">Interval Count</label><br><input type="number" name="interval_count" min="0" class="form-control @error('interval_count') is-invalid @enderror" value="{{ old('interval_count', $query->interval_count ?? "") }}" placeholder="Enter Interval Count..." id="view_interval_count" disabled>@error('interval_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="usage_type">Usage Type</label><br><input type="text" name="usage_type" class="form-control @error('usage_type') is-invalid @enderror" value="{{ old('usage_type', $query->usage_type ?? "") }}" placeholder="Enter Usage Type..." id="view_usage_type" disabled>@error('usage_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="tiers">Tiers</label><br><input type="text" name="tiers" class="form-control @error('tiers') is-invalid @enderror" value="{{ old('tiers', $query->tiers ?? "") }}" placeholder="Enter Tiers..." id="view_tiers" disabled>@error('tiers')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="transformations">Transformations</label><br><input type="text" name="transformations" class="form-control @error('transformations') is-invalid @enderror" value="{{ old('transformations', $query->transformations ?? "") }}" placeholder="Enter Transformations..." id="view_transformations" disabled>@error('transformations')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="stripe_price_id">Stripe Price Id</label><br><input type="text" name="stripe_price_id" class="form-control @error('stripe_price_id') is-invalid @enderror" value="{{ old('stripe_price_id', $query->stripe_price_id ?? "") }}" placeholder="Enter Stripe Price Id..." id="view_stripe_price_id" disabled>@error('stripe_price_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="active_from">Active From</label><br><input type="datetime-local" name="active_from" class="form-control @error('active_from') is-invalid @enderror" value="{{ old('active_from', $query->active_from ?? "") }}" placeholder="Enter Active From..." id="view_active_from" disabled>@error('active_from')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="active_to">Active To</label><br><input type="datetime-local" name="active_to" class="form-control @error('active_to') is-invalid @enderror" value="{{ old('active_to', $query->active_to ?? "") }}" placeholder="Enter Active To..." id="view_active_to" disabled>@error('active_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div><div class="mb-3 text-right">
                              <a type="button" class="btn bg-danger text-white" href="{{ route('admin.plan-prices.index') }}">Close</a>
                          </div>
                      </div>
                  </form>