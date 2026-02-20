<form method="POST" id="createsubscription_order_itemsForm" action="{{ url('api/v1/subscription-order-items') }}" enctype="multipart/form-data">
    @csrf
    @method('POST')
    <div class="row">
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="subscription_order_id">Subscription Order Id</label>
        <input type="text" name="subscription_order_id" class="form-control @error('subscription_order_id') is-invalid @enderror" value="{{ old('subscription_order_id') }}" placeholder="Enter Subscription Order Id..." id="create_subscription_order_id" required>
        @error('subscription_order_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="plan_id">Plan Id</label>
        <input type="text" name="plan_id" class="form-control @error('plan_id') is-invalid @enderror" value="{{ old('plan_id') }}" placeholder="Enter Plan Id..." id="create_plan_id" required>
        @error('plan_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="user_id">User Id</label>
        <input type="text" name="user_id" class="form-control @error('user_id') is-invalid @enderror" value="{{ old('user_id') }}" placeholder="Enter User Id..." id="create_user_id" required>
        @error('user_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="plan_name">Plan Name</label>
        <input type="text" name="plan_name" class="form-control @error('plan_name') is-invalid @enderror" value="{{ old('plan_name') }}" placeholder="Enter Plan Name..." id="create_plan_name" required>
        @error('plan_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="billing_cycle">Billing Cycle</label>
        <input type="text" name="billing_cycle" class="form-control @error('billing_cycle') is-invalid @enderror" value="{{ old('billing_cycle') }}" placeholder="Enter Billing Cycle..." id="create_billing_cycle" required>
        @error('billing_cycle')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="quantity">Quantity</label>
        <input type="number" name="quantity" min="0" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" placeholder="Enter Quantity..." id="create_quantity" required>
        @error('quantity')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="recipient_user_id">Recipient User Id</label>
        <input type="text" name="recipient_user_id" class="form-control @error('recipient_user_id') is-invalid @enderror" value="{{ old('recipient_user_id') }}" placeholder="Enter Recipient User Id..." id="create_recipient_user_id" required>
        @error('recipient_user_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="recipient_info">Recipient Info</label>
        <input type="text" name="recipient_info" class="form-control @error('recipient_info') is-invalid @enderror" value="{{ old('recipient_info') }}" placeholder="Enter Recipient Info..." id="create_recipient_info" required>
        @error('recipient_info')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="unit_price">Unit Price</label>
        <input type="number" step="any" name="unit_price" min="0" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price') }}" placeholder="Enter Unit Price..." id="create_unit_price" required>
        @error('unit_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="amount">Amount</label>
        <input type="number" step="any" name="amount" min="0" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" placeholder="Enter Amount..." id="create_amount" required>
        @error('amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="tax_amount">Tax Amount</label>
        <input type="number" step="any" name="tax_amount" min="0" class="form-control @error('tax_amount') is-invalid @enderror" value="{{ old('tax_amount') }}" placeholder="Enter Tax Amount..." id="create_tax_amount" required>
        @error('tax_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="discount_amount">Discount Amount</label>
        <input type="number" step="any" name="discount_amount" min="0" class="form-control @error('discount_amount') is-invalid @enderror" value="{{ old('discount_amount') }}" placeholder="Enter Discount Amount..." id="create_discount_amount" required>
        @error('discount_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="total_amount">Total Amount</label>
        <input type="number" step="any" name="total_amount" min="0" class="form-control @error('total_amount') is-invalid @enderror" value="{{ old('total_amount') }}" placeholder="Enter Total Amount..." id="create_total_amount" required>
        @error('total_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="start_date">Start Date</label>
        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" id="create_start_date" required>
        @error('start_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="end_date">End Date</label>
        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" id="create_end_date" required>
        @error('end_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
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
        <label for="subscription_status">Subscription Status</label>
        <input type="text" name="subscription_status" class="form-control @error('subscription_status') is-invalid @enderror" value="{{ old('subscription_status') }}" placeholder="Enter Subscription Status..." id="create_subscription_status" required>
        @error('subscription_status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="processing_error">Processing Error</label>
        <textarea name="processing_error" class="form-control @error('processing_error') is-invalid @enderror" placeholder="Enter Processing Error..." id="create_processing_error" required>{{ old('processing_error') }}</textarea>
        @error('processing_error')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="processed_at">Processed At</label>
        <input type="datetime-local" name="processed_at" class="form-control @error('processed_at') is-invalid @enderror" value="{{ old('processed_at') }}" id="create_processed_at" required>
        @error('processed_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
        <div class="mb-3 text-right">
            <a type="button" class="btn bg-danger text-white" href="{{ route('admin.subscription-order-items.index') }}">Cancel</a>
            <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>