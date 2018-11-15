<?php

namespace App\Modules\Hashes\Services;

use App\Models\Model;
use App\Modules\Hashes\Services\DescriptionParser;
use App\Models\EnumValue;
use App\Models\SourceRevCvsTag;
use App\Models\SourceRevTtsKey;
use App\Modules\Issues\Models\Issue;

class Tags
{
    /**
     * @var Model
     */
    protected $hashCommit;

    /**
     * @var DescriptionParser
     */
    protected $parser;

    public function __construct(Model $hashCommit, DescriptionParser $parser)
    {
        $this->hashCommit = $hashCommit;
        $this->parser     = $parser;
    }

    protected function saveTag($key, $comment)
    {
        $cvsTag     = EnumValue::where('key', $key)->select('id')->first();
        $revLogType = EnumValue::where('key', $this->hashCommit->repo_module)->select('id')->first();

        if (is_null($cvsTag) || is_null($revLogType)) {
            return false;
        }

        $sourceRevCvsTag = new SourceRevCvsTag([
            'source_rev_id'   => $this->hashCommit->id,
            'cvs_tag_enum_id' => $cvsTag->id,
            'cvs_tag_comment' => $comment,
            'rev_log_type_id' => $revLogType->id
        ]);

        if ($sourceRevCvsTag->save()) {
            return $sourceRevCvsTag;
        }

        return false;
    }

    protected function saveTtsKeys($sourceRevTagId)
    {
        $ttsKeys = $this->parser->getTtsKeys();
        $issues  = Issue::setEagerLoads([])->whereIn('tts_id', $ttsKeys)->get(['id', 'tts_id']);

        foreach ($ttsKeys as $sortIndex => $ttsKey) {
            $issue = $issues->firstWhere('tts_id', $ttsKey);

            $sourceRevTtsKey = new SourceRevTtsKey([
                'source_rev_tag_id' => $sourceRevTagId,
                'cvs_tag_tts_key'   => $ttsKey,
                'sortindex'         => $sortIndex,
                'issue_id'          => $issue ? $issue->id : null
            ]);
            $sourceRevTtsKey->save();
        }
    }

    protected function saveDependencies($sourceRevTagId)
    {
        $dependencies = $this->parser->getDependencies();
        foreach ($dependencies as $dependency) {
            $dependency = new Dependency($dependency);
            var_dump($dependency);
        }
        exit;
    }

    public function save()
    {
        if ($this->parser->hasTags()) {
            foreach ($this->parser->getTags() as $key => $comment) {
                $sourceRevCvsTag = $this->saveTag($key, $comment);
                if ($sourceRevCvsTag) {
                    switch ($key) {
                        case DescriptionParser::TTS_KEYS_KEY:
                            $this->saveTtsKeys($sourceRevCvsTag->id);
                            break;
                        case DescriptionParser::DEPENDENCIES_KEY:
                            $this->saveDependencies($sourceRevCvsTag->id);
                            break;
                    }
                }
            }
        }
    }
}
