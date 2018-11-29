<?php

namespace Modules\Hashes\Services;

use App\Models\CommitMerge;
use App\Models\Dependency;
use Modules\Core\Models\Model;
use App\Models\EnumValue;
use App\Models\SourceRevCvsTag;
use App\Models\SourceRevTtsKey;
use Modules\Issues\Models\Issue;
use Illuminate\Support\Facades\Log;

class TagsService
{
    /**
     * @var Model
     */
    protected $hashCommit;

    /**
     * @var DescriptionParserService
     */
    protected $parser;

    /**
     * TagsService constructor
     *
     * @param Model $hashCommit
     * @param DescriptionParserService $parser
     */
    public function __construct(Model $hashCommit, DescriptionParserService $parser)
    {
        $this->hashCommit = $hashCommit;
        $this->parser     = $parser;
    }

    /**
     * Save tag
     *
     * @param string $key
     * @param string $comment
     * @return SourceRevCvsTag|false
     */
    protected function saveTag(string $key, string $comment)
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
            Log::channel('tags')->info("Tag '{$comment}' saved successfully");
            return $sourceRevCvsTag;
        }

        Log::channel('tags')->warning("Tag '{$comment}' was not saved");
        return false;
    }

    /**
     * Save TTS KEYS
     *
     * @param $sourceRevTagId
     */
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

            if ($sourceRevTtsKey->save()) {
                Log::channel('tags')->info(
                    "TTS KEY '{$ttsKey}' saved successfully with issue_id '{$sourceRevTtsKey->issue_id}'"
                );
                continue;
            }

            Log::channel('tags')->warning("TTS KEY '{$ttsKey}' was not saved");
        }
    }

    /**
     * Save dependencies
     *
     * @param $sourceRevTagId
     */
    protected function saveDependencies($sourceRevTagId)
    {
        $dependencies = $this->parser->getDependencies();
        foreach ($dependencies as $dependency) {
            $dependencyService = new DependencyService($dependency);

            if (is_null($dependencyService->depId)) {
                Log::channel('tags')->warning("Could not validate dependency: {$dependency}");
                continue;
            }

            $dependencyModel = new Dependency([
                'rev_id'      => $sourceRevTagId,
                'rev_type_id' => 'hash',
                'dep_id'      => $dependencyService->depId,
                'dep_type_id' => $dependencyService->type === 'table' ? 'table' : 'source',
                'functional'  => 0,
                'comment'     => 'auto-records-for-hash-commits',
                'added_by'    => 0, // this is mmpi_auto
                'deleted'     => 0
            ]);

            if ($dependencyModel->save()) {
                Log::channel('tags')->info("Dependency '{$dependency}' saved successfully");
                continue;
            }

            Log::channel('tags')->warning("Dependency '{$dependency}' was not saved");
        }
    }

    /**
     * Save merges
     *
     * @param $sourceRevTagId
     */
    protected function saveMerges($sourceRevTagId)
    {
        $merge = $this->parser->getMerge();

        if (!preg_match('/[0-9a-f]{40}/i', $merge)) {
            Log::channel('tags')->warning("Could not validate commit merge: {$merge}");
            return;
        }

        // TODO: Do not hard-code this if possible!
        // I have taken this from ivasilev's code. Not sure if it is correct like this!
        $commitLogType = EnumValue::where('type', 'revision_log_type')->where('key', 'imx_be')->select('id')->first();

        $commitMerge = new CommitMerge([
            'commit_log_type_id' => $commitLogType->id,
            'commit_id'          => $this->hashCommit->id,
            'merge_commit'       => $merge
        ]);

        if ($commitMerge->save()) {
            Log::channel('tags')->info("Commit merge '{$merge}' saved successfully");
            return;
        }

        Log::channel('tags')->warning("Commit merge '{$merge}' was not saved");
    }

    public function save()
    {
        if ($this->parser->hasNoTags()) {
            Log::channel('tags')->info("No tags detected");
            return true;
        }

        foreach ($this->parser->getTags() as $key => $comment) {
            $sourceRevCvsTag = $this->saveTag($key, $comment);
            if ($sourceRevCvsTag) {
                switch ($key) {
                    case $this->parser::TTS_KEYS_KEY:
                        $this->saveTtsKeys($sourceRevCvsTag->id);
                        break;
                    case $this->parser::DEPENDENCIES_KEY:
                        $this->saveDependencies($sourceRevCvsTag->id);
                        break;
                    case $this->parser::MERGE_KEY:
                        $this->saveMerges($sourceRevCvsTag->id);
                        break;
                }
            }
        }
    }
}
