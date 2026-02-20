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
                                        <label for="key">Key</label><br><input type="text" name="key" class="form-control @error('key') is-invalid @enderror" value="{{ old('key', $query->key ?? "") }}" placeholder="Enter Key..." id="view_key" disabled>@error('key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="max_attempts">Max Attempts</label><br><input type="number" name="max_attempts" min="0" class="form-control @error('max_attempts') is-invalid @enderror" value="{{ old('max_attempts', $query->max_attempts ?? "") }}" placeholder="Enter Max Attempts..." id="view_max_attempts" disabled>@error('max_attempts')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="decay_seconds">Decay Seconds</label><br><input type="number" name="decay_seconds" min="0" class="form-control @error('decay_seconds') is-invalid @enderror" value="{{ old('decay_seconds', $query->decay_seconds ?? "") }}" placeholder="Enter Decay Seconds..." id="view_decay_seconds" disabled>@error('decay_seconds')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="remaining">Remaining</label><br><input type="number" name="remaining" min="0" class="form-control @error('remaining') is-invalid @enderror" value="{{ old('remaining', $query->remaining ?? "") }}" placeholder="Enter Remaining..." id="view_remaining" disabled>@error('remaining')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="resets_at">Resets At</label><br><input type="datetime-local" name="resets_at" class="form-control @error('resets_at') is-invalid @enderror" value="{{ old('resets_at', $query->resets_at ?? "") }}" placeholder="Enter Resets At..." id="view_resets_at" disabled>@error('resets_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div><div class="mb-3 text-right">
                              <a type="button" class="btn bg-danger text-white" href="{{ route('admin.rate-limits.index') }}">Close</a>
                          </div>
                      </div>
                  </form>