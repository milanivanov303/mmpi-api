<?php

use Modules\Hashes\Services\DescriptionParserService;

class DescriptionParserServiceTest extends TestCase
{
    public function test_can_parse_description()
    {
        $parser = new DescriptionParserService('
            TTS KEY*:      IXDEV-7266
            IXDEV-7290
            FUNC CHANGES*: Add fix of generation of suffix for adresse and tty
            TECH CHANGES*: Add check on sessions with same adresse
            MERGE:         c00be5a14a4c6b69af8f45b7e3390187c7da822a
            DEPENDENCIES:  G_BU, G_INDIVIDU, EXT_SYS_INTERVENANTS
        ');

        $this->assertTrue($parser->hasTags());
        $this->assertFalse($parser->hasNoTags());
        $this->assertEquals(['IXDEV-7266', 'IXDEV-7290'], $parser->getTtsKeys());
        $this->assertEquals('Add fix of generation of suffix for adresse and tty', $parser->getFuncChanges());
        $this->assertEquals('Add check on sessions with same adresse', $parser->getTechChanges());
        $this->assertEquals('c00be5a14a4c6b69af8f45b7e3390187c7da822a', $parser->getMerge());
        $this->assertEquals(['G_BU', 'G_INDIVIDU', 'EXT_SYS_INTERVENANTS'], $parser->getDependencies());
        $this->assertEmpty($parser->getTests());
        $this->assertEmpty($parser->getSubject());
        $this->assertEmpty($parser->getOtherDependencies());
    }

    public function test_return_false_when_no_tags_in_description()
    {
        $parser = new DescriptionParserService(
            'IXDEV-1650 e_honor_param backend add MOLO as mvn profile'
        );

        $this->assertFalse($parser->hasTags());
        $this->assertTrue($parser->hasNoTags());
    }

    public function test_get_description()
    {
        $description = 'IXDEV-1650 e_honor_param backend add MOLO as mvn profile';

        $parser = new DescriptionParserService($description);

        $this->assertEquals($description, $parser->getDescription());
    }
}
