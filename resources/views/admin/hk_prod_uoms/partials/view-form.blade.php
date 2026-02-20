<form><div class="row"><div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="code">Code</label><br><input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $query->code ?? "") }}" placeholder="Enter Code..." id="view_code" disabled>@error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="name">Name</label><br><input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $query->name ?? "") }}" placeholder="Enter Name..." id="view_name" disabled>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                        <label for="sequence">Sequence</label><br><input type="number" name="sequence" min="0" class="form-control @error('sequence') is-invalid @enderror" value="{{ old('sequence', $query->sequence ?? "") }}" placeholder="Enter Sequence..." id="view_sequence" disabled>@error('sequence')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div><div class="mb-3 text-right">
                              <a type="button" class="btn bg-danger text-white" href="{{ route('admin.hk-prod-uoms.index') }}">Close</a>
                          </div>
                      </div>
                  </form>