<?php

namespace Modules\Hashes\Services;

use App\Models\CommitMerge;
use App\Models\Dependency;
use Core\Models\Model;
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
    protected $description;

    /**
     * TagsService constructor
     *
     * @param Model $hashCommit
     * @param DescriptionParserService $description
     */
    public function __construct(Model $hashCommit, DescriptionParserService $description)
    {
        $this->hashCommit  = $hashCommit;
        $this->description = $description;
    }

    protected function getRevisionLogType()
    {
        return app(EnumValue::class)
            ::where('type', 'revision_log_type')
            ->where('key', 'imx_be')
            ->value('id');
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
        $cvsTagId = app(EnumValue::class)
                      ::where('type', 'cvs_log_tags_stack')
                      ->where('key', $key)
                      ->value('id');

        $revLogTypeId = $this->getRevisionLogType();

        if (is_null($cvsTagId) || is_null($revLogTypeId)) {
            return false;
        }

        $sourceRevCvsTag = new SourceRevCvsTag([
            'source_rev_id'   => $this->hashCommit->id,
            'cvs_tag_enum_id' => $cvsTagId,
            'cvs_tag_comment' => $comment,
            'rev_log_type_id' => $revLogTypeId
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
        $ttsKeys = $this->description->getTtsKeys();
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
        $dependencies = $this->description->getDependencies();
        foreach ($dependencies as $dependency) {
            $dependencyService = new DependencyService($dependency);

            if (is_null($dependencyService->depId)) {
                Log::channel('tags')->warning("Could not validate dependency: {$dependency}");
                continue;
            }

            $depTypeId = $dependencyService->type === 'table' ? 'table' : 'source';

            $dependencyModel = new Dependency([
                'rev_id'      => $sourceRevTagId,
                'rev_type_id' => 'hash',
                'dep_id'      => $dependencyService->depId,
                'dep_type_id' => $depTypeId,
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
     * Save merge
     */
    protected function saveMerge()
    {
        $merge = $this->description->getMerge();

        if (!preg_match('/[0-9a-f]{40}/i', $merge)) {
            Log::channel('tags')->warning("Could not validate commit merge: {$merge}");
            return;
        }

        // I have taken this from ivasilev's code. Not sure if it is correct like this!
        $commitLogTypeId = app(EnumValue::class)::where('type', 'revision_log_type')
                            ->where('key', $this->hashCommit->repoType->key)
                            ->value('id');

        if (is_null($commitLogTypeId)) {
            Log::channel('tags')->warning(
                "Could not find commit log type for repo type '{$this->hashCommit->repoType->key}'"
            );
            return;
        }

        $commitMerge = new CommitMerge([
            'commit_log_type_id' => $commitLogTypeId,
            'commit_id' => $this->hashCommit->id,
            'merge_commit' => $merge
        ]);

        if ($commitMerge->save()) {
            Log::channel('tags')->info("Commit merge '{$merge}' saved successfully");
            return;
        }

        Log::channel('tags')->warning("Commit merge '{$merge}' was not saved");
    }

    /**
     * Clear old tags before inserting new
     */
    protected function clearTags()
    {
        $revLogTypeId = $this->getRevisionLogType();

        $sourceRevCvsTagIds = app(SourceRevCvsTag::class)
            ::where('source_rev_id', $this->hashCommit->id)
            ->where('rev_log_type_id', $revLogTypeId)
            ->pluck('id')
            ->toArray();

        app(SourceRevTtsKey::class)::whereIn('source_rev_tag_id', $sourceRevCvsTagIds)->delete();
        app(Dependency::class)::whereIn('rev_id', $sourceRevCvsTagIds)->delete();
        app(CommitMerge::class)::where('commit_id', $this->hashCommit->id)->delete();
        app(SourceRevCvsTag::class)::where('source_rev_id', $this->hashCommit->id)->delete();
    }

    /**
     * Save
     *
     * @return bool
     */
    public function save()
    {
        $this->clearTags();

        if ($this->description->hasNoTags()) {
            Log::channel('tags')->info("No tags detected");
            return true;
        }

        foreach ($this->description->getTags() as $key => $comment) {
            $sourceRevCvsTag = $this->saveTag($key, $comment);
            if ($sourceRevCvsTag) {
                switch ($key) {
                    case $this->description::TTS_KEYS_KEY:
                        $this->saveTtsKeys($sourceRevCvsTag->id);
                        break;
                    case $this->description::DEPENDENCIES_KEY:
                        $this->saveDependencies($sourceRevCvsTag->id);
                        break;
                    case $this->description::MERGE_KEY:
                        $this->saveMerge();
                        break;
                }
            }
        }
    }
}
