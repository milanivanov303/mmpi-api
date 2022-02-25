<?php

namespace Modules\Projects\Models;

use App\Models\UserProjectRole;
use App\Models\UserProjectRoleTmp;
use Core\Models\Model;
use App\Models\User;
use App\Models\EnumValue;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\DeliveryChains\Models\DeliveryChain;
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
        'project_type',
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
        'extranet_version',
        'tts_dev_project_key',
        'e_reggest_mntd_by_clnt_id',
        'v_menu_mntd_by_clnt_id',
        'std_release_organization'
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
    protected function typeBusiness() : BelongsToMany
    {
        return $this->belongsToMany(
            EnumValue::class,
            'project_specifics',
            'project_id',
            'prj_specific_feature_id'
        )
        ->select('enum_values.*')
        ->where('type', 'project_specific_feature')
        ->where('subtype', 'imx_activity');
    }

    /**
     * Get activity
     */
    protected function activity()
    {
        try {
            return $this->belongsTo(EnumValue::class, 'activity');
        } catch (\Throwable $e) {
            return $this->belongsTo(EnumValue::class);
        }
    }

    /**
     * Get group
     */
    protected function group()
    {
        return $this->belongsTo(EnumValue::class, 'group_id');
    }

    /**
     * Get project type
     */
    protected function projectType()
    {
        return $this->belongsTo(EnumValue::class, 'project_type');
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
        return $this->hasMany(UserProjectRole::class)->select('project_id');
    }

    /**
     * Get temporary roles
     */
    protected function rolesTmp()
    {
        return $this->hasMany(UserProjectRoleTmp::class)->select('project_id');
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
        try {
            return $this->belongsTo(EnumValue::class, 'intranet_version');
        } catch (\Throwable $e) {
            return $this->belongsTo(EnumValue::class);
        }
    }

    /**
     * Get project extranet version
     */
    protected function extranetVersion()
    {
        try {
            return $this->belongsTo(EnumValue::class, 'extranet_version');
        } catch (\Throwable $e) {
            return $this->belongsTo(EnumValue::class);
        }
    }

    /**
     * Get project languages
     *
     * @return BelongsToMany
     */
    protected function languages() : BelongsToMany
    {
        return $this->belongsToMany(
            EnumValue::class,
            'project_specifics',
            'project_id',
            'prj_specific_feature_id'
        )
        ->select('enum_values.*')
        ->selectRaw('project_specifics.value as priority')
        ->where('type', 'project_specific_feature')
        ->where('subtype', 'project_appl_language');
    }

    /**
     * Get numeric client code
     *
     */
    protected function numericClientCode()
    {
        return $this->belongsToMany(
            EnumValue::class,
            'project_specifics',
            'project_id',
            'prj_specific_feature_id'
        )
        ->selectRaw('project_specifics.value as client_code')
        ->whereIn(
            'prj_specific_feature_id',
            EnumValue::where('type', 'project_specific_feature')
            ->where('subtype', 'numeric_clnt_code')
            ->pluck('id')
        );
    }

    /**
     * Get e_reggest_mntd_by_clnt_id
     */
    protected function eReggestMntdByClntId()
    {
        return $this->belongsTo(EnumValue::class, 'e_reggest_mntd_by_clnt_id');
    }

    /**
     * Get v_menu_mntd_by_clnt_id
     */
    protected function vMenuMntdByClntId()
    {
        return $this->belongsTo(EnumValue::class, 'v_menu_mntd_by_clnt_id');
    }
    /**
     * Get std_release_organization
     */
    protected function stdReleaseOrganization()
    {
        return $this->belongsTo(EnumValue::class, 'std_release_organization');
    }
}
