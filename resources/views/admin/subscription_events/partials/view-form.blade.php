<form><div class="row"><div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="subscription_id">Subscription Id</label><br><input type="text" name="subscription_id" class="form-control @error('subscription_id') is-invalid @enderror" value="{{ old('subscription_id', $query->subscription_id ?? "") }}" placeholder="Enter Subscription Id..." id="view_subscription_id" disabled>@error('subscription_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="type">Type</label><br><input type="text" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type', $query->type ?? "") }}" placeholder="Enter Type..." id="view_type" disabled>@error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="data">Data</label><br><input type="text" name="data" class="form-control @error('data') is-invalid @enderror" value="{{ old('data', $query->data ?? "") }}" placeholder="Enter Data..." id="view_data" disabled>@error('data')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="changes">Changes</label><br><input type="text" name="changes" class="form-control @error('changes') is-invalid @enderror" value="{{ old('changes', $query->changes ?? "") }}" placeholder="Enter Changes..." id="view_changes" disabled>@error('changes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="causer_id">Causer Id</label><br><input type="text" name="causer_id" class="form-control @error('causer_id') is-invalid @enderror" value="{{ old('causer_id', $query->causer_id ?? "") }}" placeholder="Enter Causer Id..." id="view_causer_id" disabled>@error('causer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="causer_type">Causer Type</label><br><input type="text" name="causer_type" class="form-control @error('causer_type') is-invalid @enderror" value="{{ old('causer_type', $query->causer_type ?? "") }}" placeholder="Enter Causer Type..." id="view_causer_type" disabled>@error('causer_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="ip_address">Ip Address</label><br><input type="text" name="ip_address" class="form-control @error('ip_address') is-invalid @enderror" value="{{ old('ip_address', $query->ip_address ?? "") }}" placeholder="Enter Ip Address..." id="view_ip_address" disabled>@error('ip_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="user_agent">User Agent</label><br><textarea name="user_agent" class="form-control @error('user_agent') is-invalid @enderror" placeholder="Enter User Agent..." id="view_user_agent" disabled>{{ old('user_agent', $query->user_agent ?? "") }}</textarea>@error('user_agent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="metadata">Metadata</label><br><input type="text" name="metadata" class="form-control @error('metadata') is-invalid @enderror" value="{{ old('metadata', $query->metadata ?? "") }}" placeholder="Enter Metadata..." id="view_metadata" disabled>@error('metadata')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="occurred_at">Occurred At</label><br><input type="datetime-local" name="occurred_at" class="form-control @error('occurred_at') is-invalid @enderror" value="{{ old('occurred_at', $query->occurred_at ?? "") }}" placeholder="Enter Occurred At..." id="view_occurred_at" disabled>@error('occurred_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div><div class="mb-3 text-right">
                              <a type="button" class="btn bg-danger text-white" href="{{ route('admin.subscription-events.index') }}">Close</a>
                          </div>
                      </div>
                  </form>