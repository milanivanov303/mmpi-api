<?php

namespace Modules\Projects\Models;

use Core\Models\Model;
use App\Models\User;
use App\Models\EnumValue;
use Modules\DeliveryChains\Models\DeliveryChain;
use App\Models\UserProjectRole;
use Modules\ProjectSpecifics\Models\ProjectSpecific;

class Project extends Model
{
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'pivot',
        'project_to_delivery_chain'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'clnt_cvs_dir',
        'pnp_type',
        'clnt_code',
        'clnt_code2',
        'src_prefix',
        'src_prefix2',
        'src_itf_prefix',
        'getdcli',
        'getdcli2',
        'activity',
        'activite_gpc',
        'activite_sdr',
        'imx_formstag',
        'forms_lng_dlvry',
        'uses_transl_upd',
        'inactive',
        'display_name',
        'sla_from',
        'sla_to',
        'type_business',
        'modified_by_id',
        'group_id',
        'country_id',
        'communication_lng_id',
        'delivery_method_id',
        'se_mntd_by_clnt_id',
        'tl_mntd_by_clnt_id',
        'njsch_mntd_by_clnt_id',
        'trans_mntd_by_clnt_id',
        'intranet_version',
        'extranet_version'
    ];

    /**
     * Get modifiedBy
     */
    protected function modifiedBy()
    {
        return $this->belongsTo(User::class, 'modified_by_id');
    }

    /**
     * Get type_business
     */
    protected function typeBusiness()
    {
        return $this->belongsTo(EnumValue::class, 'type_business');
    }

    /**
     * Get activity
     */
    protected function activity()
    {
        return $this->belongsTo(EnumValue::class, 'activity');
    }

    /**
     * Get group
     */
    protected function group()
    {
        return $this->belongsTo(EnumValue::class, 'group_id');
    }

    /**
     * Get group
     */
    protected function country()
    {
        return $this->belongsTo(EnumValue::class, 'country_id');
    }

    /**
     * Get group
     */
    protected function communicationLng()
    {
        return $this->belongsTo(EnumValue::class, 'communication_lng_id');
    }

    /**
     * Get delivery method
     */
    protected function deliveryMethod()
    {
        return $this->belongsTo(EnumValue::class, 'delivery_method_id');
    }

    /**
     * Get se_mntd_by_clnt
     */
    protected function seMntdByClnt()
    {
        return $this->belongsTo(EnumValue::class, 'se_mntd_by_clnt_id');
    }

    /**
     * Get tl_mntd_by_clnt
     */
    protected function tlMntdByClnt()
    {
        return $this->belongsTo(EnumValue::class, 'tl_mntd_by_clnt_id');
    }

    /**
     * Get njsch_mntd_by_clnt
     */
    protected function njschMntdByClnt()
    {
        return $this->belongsTo(EnumValue::class, 'njsch_mntd_by_clnt_id');
    }

    /**
     * Get trans_mntd_by_clnt
     */
    protected function transMntdByClnt()
    {
        return $this->belongsTo(EnumValue::class, 'trans_mntd_by_clnt_id');
    }

    /**
     * Get delivery_chains
     */
    protected function deliveryChains()
    {
        return $this->belongsToMany(DeliveryChain::class, 'project_to_delivery_chain')->active();
    }

    /**
     * Get active delivery_chains
     */
    public function scopeActive($query)
    {
        return $query->where('inactive', '=', '0');
    }

    /**
     * Get roles
     */
    protected function roles()
    {
        return $this->hasMany(UserProjectRole::class);
    }

    /**
     * Get project specifics
     */
    protected function projectSpecifics()
    {
        return $this->hasMany(ProjectSpecific::class);
    }

    /**
     * Get project intranet version
     */
    protected function intranetVersion()
    {
        return $this->belongsTo(EnumValue::class, 'intranet_version');
    }

    /**
     * Get project extranet version
     */
    protected function extranetVersion()
    {
        return $this->belongsTo(EnumValue::class, 'extranet_version');
    }
    
    public function getTypeBusinessAttribute($value)
    {
        return (int)$value;
    }
}
