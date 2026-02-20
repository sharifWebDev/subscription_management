<form method="POST" id="createsubscription_eventsForm" action="{{ url('api/v1/subscription-events') }}" enctype="multipart/form-data">
    @csrf
    @method('POST')
    <div class="row">
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="subscription_id">Subscription Id</label>
        <input type="text" name="subscription_id" class="form-control @error('subscription_id') is-invalid @enderror" value="{{ old('subscription_id') }}" placeholder="Enter Subscription Id..." id="create_subscription_id" required>
        @error('subscription_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="type">Type</label>
        <input type="text" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type') }}" placeholder="Enter Type..." id="create_type" required>
        @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="data">Data</label>
        <input type="text" name="data" class="form-control @error('data') is-invalid @enderror" value="{{ old('data') }}" placeholder="Enter Data..." id="create_data" required>
        @error('data')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="changes">Changes</label>
        <input type="text" name="changes" class="form-control @error('changes') is-invalid @enderror" value="{{ old('changes') }}" placeholder="Enter Changes..." id="create_changes" required>
        @error('changes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="causer_id">Causer Id</label>
        <input type="text" name="causer_id" class="form-control @error('causer_id') is-invalid @enderror" value="{{ old('causer_id') }}" placeholder="Enter Causer Id..." id="create_causer_id" required>
        @error('causer_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="causer_type">Causer Type</label>
        <input type="text" name="causer_type" class="form-control @error('causer_type') is-invalid @enderror" value="{{ old('causer_type') }}" placeholder="Enter Causer Type..." id="create_causer_type" required>
        @error('causer_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="ip_address">Ip Address</label>
        <input type="text" name="ip_address" class="form-control @error('ip_address') is-invalid @enderror" value="{{ old('ip_address') }}" placeholder="Enter Ip Address..." id="create_ip_address" required>
        @error('ip_address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="user_agent">User Agent</label>
        <textarea name="user_agent" class="form-control @error('user_agent') is-invalid @enderror" placeholder="Enter User Agent..." id="create_user_agent" required>{{ old('user_agent') }}</textarea>
        @error('user_agent')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="metadata">Metadata</label>
        <input type="text" name="metadata" class="form-control @error('metadata') is-invalid @enderror" value="{{ old('metadata') }}" placeholder="Enter Metadata..." id="create_metadata" required>
        @error('metadata')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="occurred_at">Occurred At</label>
        <input type="datetime-local" name="occurred_at" class="form-control @error('occurred_at') is-invalid @enderror" value="{{ old('occurred_at') }}" id="create_occurred_at" required>
        @error('occurred_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
        <div class="mb-3 text-right">
            <a type="button" class="btn bg-danger text-white" href="{{ route('admin.subscription-events.index') }}">Cancel</a>
            <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>