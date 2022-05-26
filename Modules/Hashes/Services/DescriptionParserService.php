<?php

namespace Modules\Hashes\Services;

use App\Models\EnumValue;
use Illuminate\Support\Facades\Log;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

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
     * Mandatory keys
     *
     * @var array
     */
    protected $mandatoryKeys = [];

    /**
     * Validation errors
     *
     * @var array
     */
    protected $errors = [];

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
        $cvsTags = app(EnumValue::class)::where('type', 'cvs_log_tags_stack')->get();

        foreach ($cvsTags as $cvsTag) {
            $this->keys[$cvsTag->key] = $cvsTag->extra_property;
            if ($cvsTag->url === 'mandatory') {
                $this->mandatoryKeys[$cvsTag->key] = $cvsTag->value;
            }
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
        $pattern = '/[\n,; ]/';

        // if dependencies remove space from pattern
        if (self::DEPENDENCIES_KEY == $key) {
            $pattern = str_replace(' ', '', $pattern);
        }

        $data = $this->getData($key);
        return array_filter(
            array_map(
                'trim',
                preg_split($pattern, $data, -1, PREG_SPLIT_NO_EMPTY)
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

    /**
     * Is valid description
     *
     * @return bool
     */
    public function isValid() : bool
    {
        if ($this->doNotValidate()) {
            return true;
        }

        // check mandatory fields
        foreach ($this->mandatoryKeys as $cvsTagKey => $cvsTagValue) {
            if (empty($this->getData($cvsTagKey))) {
                $this->errors[] = "{$cvsTagValue} is mandatory";
            }
        }

        // check tts tickets
        try {
            $issueService = new IssueService();
            foreach ($this->getTtsKeys() as $ttsKey) {
                try {
                    $issueService->get($ttsKey);
                } catch (JiraException $e) {
                    if ($e->getCode() === 404) {
                        $this->errors[] = "Ticket {$ttsKey} does not exists in TTS";
                    }
                    if ($e->getCode() === 401) {
                        $this->errors[] = "We were unable to validate ticket {$ttsKey} exists in TTS";
                    }
                    Log::channel('tags')->error($e->getMessage());
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return count($this->errors) === 0;
    }

    /**
     * Do not validate description
     *
     * @return bool
     */
    protected function doNotValidate() : bool
    {
        // commit is generated by maven plugin, so do not validate it
        if (preg_match('/\[maven-release-plugin\]/', $this->description)) {
            return true;
        }
        
        // Merge heads is a standard comment when merging competing heads in the same branch
        if (preg_match('/Merge heads/', $this->description)) {
            return true;
        }

        // Тhe FE release commit
        if (preg_match('/FE-RELEASE-COMMIT-/', $this->description)) {
            return true;
        }

        // All merge related commit messages
        if (preg_match('/Merge (remote-tracking )?branch .*/', $this->description)) {
            return true;
        }

        // Rhode pull requests merge
        if (preg_match('/Merge pull request .*/', $this->description)) {
            return true;
        }

        //CUP-38218 FUP - CODIX - add exception for tag hash messages
        //(Added tag CHRONO OPTII for changeset 4a31f20e08f1)
        if (preg_match('/Added tag .*for changeset/', $this->description)) {
            return true;
        }

        return false;
    }

    /**
     * Get validation errors
     *
     * @return array
     */
    public function getErrors() : array
    {
        return $this->errors;
    }
}
