<form><div class="row"><div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="name">Name</label><br><input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $query->name ?? "") }}" placeholder="Enter Name..." id="view_name" disabled>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="code">Code</label><br><input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $query->code ?? "") }}" placeholder="Enter Code..." id="view_code" disabled>@error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="description">Description</label><br><textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Enter Description..." id="view_description" disabled>{{ old('description', $query->description ?? "") }}</textarea>@error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="type">Type</label><br><input type="text" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type', $query->type ?? "") }}" placeholder="Enter Type..." id="view_type" disabled>@error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="billing_period">Billing Period</label><br><input type="text" name="billing_period" class="form-control @error('billing_period') is-invalid @enderror" value="{{ old('billing_period', $query->billing_period ?? "") }}" placeholder="Enter Billing Period..." id="view_billing_period" disabled>@error('billing_period')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="billing_interval">Billing Interval</label><br><input type="number" name="billing_interval" min="0" class="form-control @error('billing_interval') is-invalid @enderror" value="{{ old('billing_interval', $query->billing_interval ?? "") }}" placeholder="Enter Billing Interval..." id="view_billing_interval" disabled>@error('billing_interval')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="is_active">Is Active</label><br><input type="radio" name="is_active" id="view_is_active_yes" value="1" {{ old('is_active', $query->is_active ?? "") == 1 ? "checked" : "" }} disabled> 
                                           <label for="editis_active_yes" disabled>Is Active Yes </label>
                                           <input type="radio" name="is_active" id="view_is_active_no" value="0" {{ old('is_active', $query->is_active ?? "") == 0 ? "checked" : "" }} disabled> 
                                           <label for="editis_active_no" disabled>Is Active No </label>@error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="is_visible">Is Visible</label><br><input type="radio" name="is_visible" id="view_is_visible_yes" value="1" {{ old('is_visible', $query->is_visible ?? "") == 1 ? "checked" : "" }} disabled> 
                                           <label for="editis_visible_yes" disabled>Is Visible Yes </label>
                                           <input type="radio" name="is_visible" id="view_is_visible_no" value="0" {{ old('is_visible', $query->is_visible ?? "") == 0 ? "checked" : "" }} disabled> 
                                           <label for="editis_visible_no" disabled>Is Visible No </label>@error('is_visible')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="sort_order">Sort Order</label><br><input type="number" name="sort_order" min="0" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $query->sort_order ?? "") }}" placeholder="Enter Sort Order..." id="view_sort_order" disabled>@error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="is_featured">Is Featured</label><br><input type="radio" name="is_featured" id="view_is_featured_yes" value="1" {{ old('is_featured', $query->is_featured ?? "") == 1 ? "checked" : "" }} disabled> 
                                           <label for="editis_featured_yes" disabled>Is Featured Yes </label>
                                           <input type="radio" name="is_featured" id="view_is_featured_no" value="0" {{ old('is_featured', $query->is_featured ?? "") == 0 ? "checked" : "" }} disabled> 
                                           <label for="editis_featured_no" disabled>Is Featured No </label>@error('is_featured')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="metadata">Metadata</label><br><input type="text" name="metadata" class="form-control @error('metadata') is-invalid @enderror" value="{{ old('metadata', $query->metadata ?? "") }}" placeholder="Enter Metadata..." id="view_metadata" disabled>@error('metadata')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div><div class="mb-3 text-right">
                              <a type="button" class="btn bg-danger text-white" href="{{ route('admin.plans.index') }}">Close</a>
                          </div>
                      </div>
                  </form>