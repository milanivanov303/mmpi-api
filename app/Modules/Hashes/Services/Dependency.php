<?php

namespace App\Modules\Hashes\Services;

use App\Models\EnumValue;

class Dependency
{
    const TAG_REV_NUM_KEY = 'cvs_tag_rev_num';

    const REV_KEYWORDS = [
        'revision',
        'version',
        '>='
    ];

    protected $name;
    protected $type;
    protected $revision;

    public function __construct($name)
    {
        $this->name     = current(explode(' ', $name));
        $this->type     = $this->extractType();

        if ($this->type !== 'table') {
            $this->revision = $this->extractRevision($name);
        }
    }

    protected function extractRevision($name)
    {
        $pattern = EnumValue::where('key', self::TAG_REV_NUM_KEY)->first()->extra_property;

        $replaceArray = array_merge([$this->name], self::REV_KEYWORDS);

        $revision = str_replace($replaceArray, '', $name);


        $revision = preg_replace('/\s+/', '', $revision);
        $revision = trim($revision, '-');

        var_dump($revision);

        preg_match($pattern, $name, $matches);

        var_dump($pattern, $matches);
    }

    public function extractType()
    {
        $pathInfo = pathinfo($this->name);

        if (!array_key_exists('extension', $pathInfo)) {
            // check if it is table
            if ($this->isTableName()) {
                return 'table';
            }
        }

        switch ($pathInfo['extension']) {
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

    protected function isTableName()
    {
        /*
        $table_name = trim(strtoupper($this->pathinfo['filename']));
        $query = "SELECT OBJECT_NAME, OBJECT_TYPE FROM user_objects WHERE OBJECT_NAME = '$table_name'";
        $res = base::$refbgDb->ociUse($query);

        if(count($res) > 0 && $res[0]['OBJECT_TYPE']=='TABLE' ){
            $this->type = 'table';
        }
        */
        return true;
    }


    public function defineName()
    {
        if (preg_match("/([^\s]+\.[^\s]+)/", $this->fname)) {
            // Check path attached
            if (preg_match("/(?<!\/)\/(?=[^\s\/]+\.[^\s\/]+)/", $this->fname, $matches)) {
                $this->fname = $this->pathinfo['basename'];
            }
            $this->selectPath();
        }
    }

    public function selectPath()
    {

        $query ="select s.*
                    from source s
                    left join source_revision sr on sr.source_id = s.source_id
                    where source_name = '{$this->fname}'
                    and revision = '{$this->revision}'";

        $getResult = base::dbUse($query);

        dbErr::getErrArray($getResult, __METHOD__);

        if (isset($getResult[0]) && isset($getResult[0]['source_path'])) {
            $this->fpath = $getResult[0]['source_path'];
        }
    }
}
