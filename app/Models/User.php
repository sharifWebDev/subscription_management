<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'email_verified_at',
        'password',
        'remember_token',
        'billing_type',
        'stripe_customer_id',
        'tax_id',
        'is_tax_exempt',
        'tax_certificate',
        'billing_address',
        'shipping_address',
        'preferred_currency',
        'preferred_payment_method',
        'auto_renew',
        'trial_ends_at',
        'has_used_trial',
        'account_status',
        'account_status_reason',
        'metadata',
        'preferences',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_tax_exempt' => 'boolean',
        'tax_certificate' => 'array',
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'auto_renew' => 'boolean',
        'trial_ends_at' => 'datetime',
        'has_used_trial' => 'boolean',
        'metadata' => 'array',
        'preferences' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function refunds()
    {
        return $this->hasMany(\App\Models\Refund::class, 'user_id');
    }

    public function invoices()
    {
        return $this->hasMany(\App\Models\Invoice::class, 'user_id');
    }
}
