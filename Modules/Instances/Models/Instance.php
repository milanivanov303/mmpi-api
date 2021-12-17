<?php

namespace Modules\Instances\Models;

use App\Models\User;
use Core\Models\Model;
use App\Models\EnumValue;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Modules\DeliveryChains\Models\DeliveryChainType;
use Modules\DeliveryChains\Models\DeliveryChain;

class Instance extends Model
{
    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'pivot'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'play_as_demo',
        'timezone',
        'host',
        'user',
        'db_user',
        'tns_name',
        'has_patch_install_in_init',
        'owner',
        'status',
        'instance_type_id',
        'environment_type_id',
        'password'
    ];

    /**
     * Get owner
     */
    protected function owner()
    {
        try {
            return $this->belongsTo(EnumValue::class, 'owner');
        } catch (\Throwable $e) {
            return $this->belongsTo(EnumValue::class);
        }
    }

    /**
     * Get instance type
     */
    protected function instanceType()
    {
        return $this->belongsTo(InstanceType::class);
    }

    /**
     * Get environment type
     */
    protected function environmentType()
    {
        return $this->belongsTo(DeliveryChainType::class);
    }

    /**
     * Get status
     */
    protected function status()
    {
        try {
            return $this->belongsTo(EnumValue::class, 'status');
        } catch (\Throwable $e) {
            return $this->belongsTo(EnumValue::class);
        }
    }

    /**
     * Get delivery_chains
     */
    protected function deliveryChains()
    {
        return $this->belongsToMany(DeliveryChain::class, 'instance_to_delivery_chain')->active();
    }

    /**
     * Get active delivery_chains
     */
    public function scopeActive($query)
    {
        return $query->whereHas('status', function ($q) {
                    $q->where('key', 'active');
        });
    }

    /**
     * @param string|null $value
     * @return string|null
     */
    public function getPasswordAttribute(?string $value) : ?string
    {
        try {
            return Crypt::decrypt($value);
        } catch (DecryptException $e) {
            return null;
        }
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setPassword(?string $value) : self
    {
        if (is_null($value)) {
            $this->attributes['password'] = null;
            return $this;
        }

        $this->attributes['password'] = Crypt::encrypt($value);
        return $this;
    }
}
