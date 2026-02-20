<form><div class="row"><div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="plan_id">Plan Id</label><br><input type="text" name="plan_id" class="form-control @error('plan_id') is-invalid @enderror" value="{{ old('plan_id', $query->plan_id ?? "") }}" placeholder="Enter Plan Id..." id="view_plan_id" disabled>@error('plan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="feature_id">Feature Id</label><br><input type="text" name="feature_id" class="form-control @error('feature_id') is-invalid @enderror" value="{{ old('feature_id', $query->feature_id ?? "") }}" placeholder="Enter Feature Id..." id="view_feature_id" disabled>@error('feature_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="value">Value</label><br><input type="text" name="value" class="form-control @error('value') is-invalid @enderror" value="{{ old('value', $query->value ?? "") }}" placeholder="Enter Value..." id="view_value" disabled>@error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="config">Config</label><br><input type="text" name="config" class="form-control @error('config') is-invalid @enderror" value="{{ old('config', $query->config ?? "") }}" placeholder="Enter Config..." id="view_config" disabled>@error('config')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="sort_order">Sort Order</label><br><input type="number" name="sort_order" min="0" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $query->sort_order ?? "") }}" placeholder="Enter Sort Order..." id="view_sort_order" disabled>@error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="is_inherited">Is Inherited</label><br><input type="radio" name="is_inherited" id="view_is_inherited_yes" value="1" {{ old('is_inherited', $query->is_inherited ?? "") == 1 ? "checked" : "" }} disabled> 
                                           <label for="editis_inherited_yes" disabled>Is Inherited Yes </label>
                                           <input type="radio" name="is_inherited" id="view_is_inherited_no" value="0" {{ old('is_inherited', $query->is_inherited ?? "") == 0 ? "checked" : "" }} disabled> 
                                           <label for="editis_inherited_no" disabled>Is Inherited No </label>@error('is_inherited')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="parent_feature_id">Parent Feature Id</label><br><input type="text" name="parent_feature_id" class="form-control @error('parent_feature_id') is-invalid @enderror" value="{{ old('parent_feature_id', $query->parent_feature_id ?? "") }}" placeholder="Enter Parent Feature Id..." id="view_parent_feature_id" disabled>@error('parent_feature_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="effective_from">Effective From</label><br><input type="datetime-local" name="effective_from" class="form-control @error('effective_from') is-invalid @enderror" value="{{ old('effective_from', $query->effective_from ?? "") }}" placeholder="Enter Effective From..." id="view_effective_from" disabled>@error('effective_from')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="effective_to">Effective To</label><br><input type="datetime-local" name="effective_to" class="form-control @error('effective_to') is-invalid @enderror" value="{{ old('effective_to', $query->effective_to ?? "") }}" placeholder="Enter Effective To..." id="view_effective_to" disabled>@error('effective_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div><div class="mb-3 text-right">
                              <a type="button" class="btn bg-danger text-white" href="{{ route('admin.plan-features.index') }}">Close</a>
                          </div>
                      </div>
                  </form>