<?php

namespace App\Modules\Hashes\Services;

use App\Models\EnumValue;

class DescriptionParser
{
    const TTS_KEYS_KEY     = 'cvs_tag_tts_key';
    const FUNC_CHANGES_KEY = 'cvs_tag_func_changes';
    const TECH_CHANGES_KEY = 'cvs_tag_tech_changes';
    const MERGES_KEY       = 'cvs_tag_merge';
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
    protected $description;

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
        $this->description = $description;

        $this->setKeys();
        $this->parse();
    }

    /**
     * Set keys to search for
     *
     */
    protected function setKeys()
    {
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

        $lines = array_filter(
            array_map(
                'trim',
                explode(self::TAG_END, $this->description)
            )
        );

        foreach ($lines as $line) {
            foreach ($this->keys as $key => $pattern) {
                if (preg_match($pattern, $line)) {
                    $this->addTagData($key, $line);
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
                preg_split('/(,)|(\n)|(;)/', $data)
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
     * @return array
     */
    public function getFuncChanges()
    {
        return $this->getData(static::FUNC_CHANGES_KEY);
    }

    /**
     * Get technical changes
     *
     * @return array
     */
    public function getTechChanges()
    {
        return $this->getData(static::TECH_CHANGES_KEY);
    }

    /**
     * Get merges
     *
     * @return array|
     */
    public function getMerges()
    {
        return $this->getData(static::MERGES_KEY);
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
     * Get subjects
     *
     * @return array
     */
    public function getSubjects()
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
