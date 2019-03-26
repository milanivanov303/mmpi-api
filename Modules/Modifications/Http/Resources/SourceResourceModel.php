<?php

namespace Modules\Modifications\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SourceResourceModel extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->resource->setVisible([
            'id',
            'name',
            'issue',
            'path',
            'contents',
            'delivery_chain',
            'version',
            'prev_version',
            'revision_converted',
            'instance',
            'instance_status',
            'comments',
            'permissions',
            'action_type',
            'created_by',
            'created_on'
        ]);

        return $this->resource->toArray();
    }
}
