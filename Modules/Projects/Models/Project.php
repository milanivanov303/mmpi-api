<?php

namespace Modules\Projects\Models;

use Modules\Core\Models\Model;
use App\Models\User;
use App\Models\EnumValue;

class Project extends Model
{
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'modifiedBy',
        'activity',
        'group',
        'country',
        'communicationLng',
        'deliveryMethod',
        'seMntdByClnt',
        'tlMntdByClnt',
        'njschMntdByClnt',
        'transMntdByClnt'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'modified_by_id',
        'group_id',
        'country_id',
        'communication_lng_id',
        'delivery_method_id',
        'se_mntd_by_clnt_id',
        'tl_mntd_by_clnt_id',
        'njsch_mntd_by_clnt_id',
        'trans_mntd_by_clnt_id'
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
        'type_business'
    ];

    /**
     * Get modifiedBy
     */
    public function modifiedBy()
    {
        return $this->belongsTo(User::class, 'modified_by_id');
    }

    /**
     * Get activity
     */
    public function activity()
    {
        return $this->belongsTo(EnumValue::class, 'activity')->minimal();
    }

    /**
     * Get group
     */
    public function group()
    {
        return $this->belongsTo(EnumValue::class, 'group_id')->minimal();
    }

    /**
     * Get group
     */
    public function country()
    {
        return $this->belongsTo(EnumValue::class, 'country_id')->minimal();
    }

    /**
     * Get group
     */
    public function communicationLng()
    {
        return $this->belongsTo(EnumValue::class, 'communication_lng_id')->minimal();
    }

    /**
     * Get delivery method
     */
    public function deliveryMethod()
    {
        return $this->belongsTo(EnumValue::class, 'delivery_method_id')->minimal();
    }

    /**
     * Get se_mntd_by_clnt
     */
    public function seMntdByClnt()
    {
        return $this->belongsTo(EnumValue::class, 'se_mntd_by_clnt_id')->minimal();
    }

    /**
     * Get tl_mntd_by_clnt
     */
    public function tlMntdByClnt()
    {
        return $this->belongsTo(EnumValue::class, 'tl_mntd_by_clnt_id')->minimal();
    }

    /**
     * Get njsch_mntd_by_clnt
     */
    public function njschMntdByClnt()
    {
        return $this->belongsTo(EnumValue::class, 'njsch_mntd_by_clnt_id')->minimal();
    }

    /**
     * Get trans_mntd_by_clnt
     */
    public function transMntdByClnt()
    {
        return $this->belongsTo(EnumValue::class, 'trans_mntd_by_clnt_id')->minimal();
    }
}
