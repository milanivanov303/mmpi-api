<?php

namespace Modules\Gitlab\Models;

use Core\Models\Model;

class Project extends Model
{
    /**
     * @param string $topic
     * @param array $projects
     * @return array
     */
    public function projectsByTopic(string $topic, array $projects) : array
    {
        return array_values(array_filter($projects, function ($project) use ($topic) {
            return in_array($topic, $project['tag_list']);
        }));
    }
}
