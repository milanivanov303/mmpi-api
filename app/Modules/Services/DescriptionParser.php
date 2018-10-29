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
     * @param mixed $data
     */
    protected function addTagData(string $key, $data)
    {
        $this->tags[$key][] = trim($data);
    }

    /**
     * Parse description
     */
    protected function parse()
    {
        /*
         * Split description to line and search in every line for keys
         * Regular expressions are writen this why. We can change them later!
         */
        $lines = explode(PHP_EOL, $this->description);

        foreach ($lines as $line) {
            foreach ($this->keys as $key => $pattern) {
                if (preg_match($pattern, $line, $match)) {
                    $this->addTagData($key, preg_replace($pattern, '', $line));
                }
            }
        }
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
     * Get TTS keys
     *
     * @return array
     */
    public function getTtsKeys()
    {
        return $this->tags[static::TTS_KEYS_KEY] ?? [];
    }

    /**
     * Get functional changes
     *
     * @return array
     */
    public function getFuncChanges()
    {
        return $this->tags[static::FUNC_CHANGES_KEY] ?? [];
    }

    /**
     * Get technical changes
     *
     * @return array
     */
    public function getTechChanges()
    {
        return $this->tags[static::TECH_CHANGES_KEY] ?? [];
    }

    /**
     * Get merges
     *
     * @return array|
     */
    public function getMerges()
    {
        return $this->tags[static::MERGES_KEY] ?? [];
    }

    /**
     * Get dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return $this->tags[static::DEPENDENCIES_KEY] ?? [];
    }

    /**
     * Get tests
     *
     * @return array
     */
    public function getTests()
    {
        return $this->tags[static::TESTS_KEY] ?? [];
    }

    /**
     * Get subjects
     *
     * @return array
     */
    public function getSubjects()
    {
        return $this->tags[static::SUBJECTS_KEY] ?? [];
    }

    /**
     * Get other dependencies
     *
     * @return array
     */
    public function getOtherDependencies()
    {
        return $this->tags[static::OTH_DEPS_KEY] ?? [];
    }
}
