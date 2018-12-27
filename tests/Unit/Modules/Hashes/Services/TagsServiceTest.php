<?php

use Modules\Hashes\Services\TagsService;
use Modules\Hashes\Services\DescriptionParserService;
use \Modules\Hashes\Models\HashCommit;
use App\Models\EnumValue;
use Core\Models\Model;

class TagsServiceTest extends TestCase
{
    public function setUp() {
        parent::setUp();

        // Mock EnumValue
        // This is not very good as expressions can be changed in DB and not in the test!!!
        $enumValueMock = Mockery::mock(EnumValue::class);
        $enumValueMock
            ->shouldReceive('where')
            ->andReturn(
                 Mockery::mock([
                    'get' => [
                        (new Model)->forceFill(['key' => 'cvs_tag_dependencies', 'extra_property' => '/DEPENDENCIES(\*)?:/']),
                        (new Model)->forceFill(['key' => 'cvs_tag_func_changes', 'extra_property' => '/FUNC[_|" "|-]{1}CHANGES(\*)?:/']),
                        (new Model)->forceFill(['key' => 'cvs_tag_merge', 'extra_property' => '/MERGE:/']),
                        (new Model)->forceFill(['key' => 'cvs_tag_oth_deps', 'extra_property' => '/OTH DEPS:/']),
                        (new Model)->forceFill(['key' => 'cvs_tag_rev_num', 'extra_property' => '/^[0-9]+[.][0-9]+([.](1|[012468]+)[.][0-9]+)*\s-\s/m']),
                        (new Model)->forceFill(['key' => 'cvs_tag_subject', 'extra_property' => '/SUBJECT:/i']),
                        (new Model)->forceFill(['key' => 'cvs_tag_tech_changes', 'extra_property' => '/TECH[_|" "|-]{1}CHANGES(\*)?:/']),
                        (new Model)->forceFill(['key' => 'cvs_tag_test', 'extra_property' => '/TESTS(\*)?:/']),
                        (new Model)->forceFill(['key' => 'cvs_tag_tts_key', 'extra_property' => '/TTS[_|" "|-]{1}(KEY|Key)(\*)?:/'])
                    ]
                 ])
            )
            ->once();

        $this->app->instance(EnumValue::class, $enumValueMock);
    }

    public function test_save_tags()
    {
        $hashCommit = new HashCommit([
            'id'                 => 123,
            'repo_module'        => 'imx_be',
            'commit_description' => '
                TTS KEY*:      IXDEV-7266
                IXDEV-7290
                FUNC CHANGES*: Add fix of generation of suffix for adresse and tty
                TECH CHANGES*: Add check on sessions with same adresse
                MERGE:         c00be5a14a4c6b69af8f45b7e3390187c7da822a
                DEPENDENCIES:  G_BU, G_INDIVIDU, EXT_SYS_INTERVENANTS
            '
        ]);

        $parser = new DescriptionParserService($this->hashCommit->commit_description);

        $tags = new TagsService($this->hashCommit, $parser);
        $tags->save();
    }
}
