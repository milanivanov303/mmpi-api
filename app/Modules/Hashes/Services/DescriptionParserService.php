<?php

namespace App\Modules\Hashes\Services;

use App\Models\EnumValue;
use Illuminate\Support\Facades\DB;

class DescriptionParserService
{
    const TTS_KEYS_KEY     = 'cvs_tag_tts_key';
    const FUNC_CHANGES_KEY = 'cvs_tag_func_changes';
    const TECH_CHANGES_KEY = 'cvs_tag_tech_changes';
    const MERGE_KEY        = 'cvs_tag_merge';
    const DEPENDENCIES_KEY = 'cvs_tag_dependencies';
    const TESTS_KEY        = 'cvs_tag_test';
    const SUBJECTS_KEY     = 'cvs_tag_subject';
    const OTH_DEPS_KEY     = 'cvs_tag_oth_deps';

    const TAG_END          = 'tag_end';

    /**
     * Hash description
     *
     * @var string
     */
    protected $description = null;

    /**
     * Parsed tags data
     *
     * @var array
     */
    protected $tags = [];

    /**
     * Keys to search for
     *
     * @var array
     */
    protected $keys = [];

    /**
     * HashDescriptionParser constructor
     *
     * @param string $description
     */
    public function __construct(string $description)
    {
        $this->setKeys();

        $this->description = $description;
        $this->parse();
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set keys to search for
     *
     */
    protected function setKeys()
    {
        DB::table('aaa');

        $enums = EnumValue::where('type', 'cvs_log_tags_stack')->get(['key', 'extra_property']);

        foreach ($enums as $enum) {
            $this->keys[$enum->key] = $enum->extra_property;
        }
    }

    /**
     * Add tag data
     *
     * @param string $key
     * @param string $data
     */
    protected function addTagData(string $key, string $data)
    {
        $this->tags[$key] = trim($data);
    }

    /**
     * Parse description
     */
    protected function parse()
    {
        // add tag_end, before every tag, so we can split by it
        foreach ($this->keys as $key => $pattern) {
            $this->description = preg_replace_callback($pattern, function ($matches) {
                return self::TAG_END . $matches[0];
            }, $this->description);
        }

        // split description by tag_end and remove empty elements
        $tags = array_filter(
            array_map(
                'trim',
                explode(self::TAG_END, $this->description)
            )
        );

        // loop tags and put them in correct section by matching defined patterns
        foreach ($tags as $tag) {
            foreach ($this->keys as $key => $pattern) {
                if (preg_match($pattern, $tag)) {
                    $this->addTagData($key, $tag);
                }
            }
        }
    }

    /**
     * Get tag data as string
     *
     * @param string $key
     * @return string
     */
    protected function getData($key)
    {
        $tag     = $this->tags[$key] ?? '';
        $pattern = $this->keys[$key];

        return trim(
            preg_replace($pattern, '', $tag)
        );
    }

    /**
     * Get tag data as array
     *
     * @param string $key
     * @return array
     */
    protected function getDataArray(string $key)
    {
        $data = $this->getData($key);
        return array_filter(
            array_map(
                'trim',
                preg_split('/[\n,;]/', $data, -1, PREG_SPLIT_NO_EMPTY)
            )
        );
    }

    /**
     * Check if there are tags for this description
     *
     * @return bool
     */
    public function hasTags()
    {
        return count($this->tags) > 0;
    }

    /**
     * Check if there are no tags for this description
     *
     * @return bool
     */
    public function hasNoTags()
    {
        return !$this->hasTags();
    }

    /**
     * Get all tags
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Get TTS keys
     *
     * @return array
     */
    public function getTtsKeys()
    {
        return $this->getDataArray(static::TTS_KEYS_KEY);
    }

    /**
     * Get functional changes
     *
     * @return string
     */
    public function getFuncChanges()
    {
        return $this->getData(static::FUNC_CHANGES_KEY);
    }

    /**
     * Get technical changes
     *
     * @return string
     */
    public function getTechChanges()
    {
        return $this->getData(static::TECH_CHANGES_KEY);
    }

    /**
     * Get merge
     *
     * @return string
     */
    public function getMerge()
    {
        return $this->getData(static::MERGE_KEY);
    }

    /**
     * Get dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return $this->getDataArray(static::DEPENDENCIES_KEY);
    }

    /**
     * Get tests
     *
     * @return array
     */
    public function getTests()
    {
        return $this->getDataArray(static::TESTS_KEY);
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->getData(static::SUBJECTS_KEY);
    }

    /**
     * Get other dependencies
     *
     * @return array
     */
    public function getOtherDependencies()
    {
        return $this->getDataArray(static::OTH_DEPS_KEY);
    }
}
