<?php

namespace Modules\Hashes\Services;

//use App\Models\EnumValue;
use App\Models\ImxTable;
use App\Modules\Sources\Models\Source;
use Modules\SourceRevisions\Models\SourceRevision;

class DependencyService
{
    const TAG_REV_NUM_KEY = 'cvs_tag_rev_num';

    /**
     * @var string
     */
    protected $data;

    /**
     * @var array
     */
    protected $pathinfo;

    /**
     * @var string
     */
    public $name;

    /**
     * @var null|string
     */
    public $column;

    /**
     * @var null|string
     */
    public $path;

    /**
     * @var null|string
     */
    public $type;

    /**
     * @var null|string
     */
    public $revision;

    /**
     * @var null|integer
     */
    public $depId = null;

    /**
     * Dependency constructor.
     *
     * @param string $data
     */
    public function __construct(string $data)
    {
        $this->data     = $data;
        $this->pathinfo = pathinfo($this->getFileName());

        $this->name     = $this->pathinfo['basename'];
        $this->type     = $this->getType();
        $this->revision = $this->getRevision();

        if ($this->type !== 'table') {
            $this->path = $this->getPath();
        }
    }

    /**
     * Get file name
     *
     * @return string
     */
    protected function getFileName()
    {
        return trim(
            current(
                explode(' ', $this->data)
            )
        );
    }

    /**
     * Get revision
     *
     * @return null|string
     */
    protected function getRevision()
    {
        // TODO: check if we can change enum pattern
        //$pattern = EnumValue::where('key', self::TAG_REV_NUM_KEY)->first()->extra_property;
        $pattern = '/[0-9]+[.][0-9]+([.](1|[012468]+)[.][0-9]+)*/m';

        if (preg_match($pattern, $this->data, $matches)) {
            return $matches[0];
        }

        return null;
    }

    /**
     * Get type
     *
     * @return null|string
     */
    protected function getType()
    {
        if (array_key_exists('extension', $this->pathinfo)) {
            switch ($this->pathinfo['extension']) {
                case "cpp":
                case "pc":
                case "pcs":
                case "c":
                    return 'file';
                case "pck":
                    return 'package';
                case "prc":
                    return 'procedure';
                case "typ":
                    return 'type';
                case "fnc":
                    return 'function';
                case "view":
                    return 'view';
            }
        }

        if ($this->isTableName()) {
            return 'table';
        }

        return null;
    }

    /**
     * Check if table
     *
     * @return bool
     */
    protected function isTableName()
    {
        // check in local tables list
        $table = app(ImxTable::class)
                    ::where('table_name', $this->getTableName())
                    ->where('column_name', $this->getColumnName())
                    ->first();

        if ($table) {
            $this->depId = $table->id;
            return true;
        }

        return false;
    }

    /**
     * Get table name
     *
     * @return null|string
     */
    protected function getTableName()
    {
        return trim(strtoupper($this->pathinfo['filename']));
    }

    /**
     * Get column name
     *
     * @return null|string
     */
    protected function getColumnName()
    {
        if (array_key_exists('extension', $this->pathinfo)) {
            return trim(strtoupper($this->pathinfo['extension']));
        }

        return null;
    }

    /**
     * Get path
     *
     * @return null|string
     */
    protected function getPath()
    {
        $source = app(Source::class)
                    ::where('source_name', $this->name)
                    ->first();

        if (is_null($source)) {
            return null;
        }

        $revision = app(SourceRevision::class)
                        ::where('source_id', $source->source_id)
                        ->where('revision', $this->revision)
                        ->first();

        if (is_null($revision)) {
            return null;
        }

        $this->depId = $revision->rev_id;
        return $source->source_path;
    }
}
