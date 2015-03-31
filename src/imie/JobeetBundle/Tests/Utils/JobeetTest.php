<?php

namespace imie\JobeetBundle\Tests\Utils;

use imie\JobeetBundle\Utils\Jobeet;

class JobeetTest extends \PHPUnit_Framework_TestCase {

    public function testSlugify() {
        $this->assertEquals('simieio', Jobeet::slugify('Simieio'));
        $this->assertEquals('simieio-labs', Jobeet::slugify('simieio labs'));
        $this->assertEquals('simieio-labs', Jobeet::slugify('simieio labs'));
        $this->assertEquals('paris-france', Jobeet::slugify('paris,france'));
        $this->assertEquals('simieio', Jobeet::slugify(' simieio'));
        $this->assertEquals('simieio', Jobeet::slugify('simieio '));

        $this->assertEquals('n-a', Jobeet::slugify(''));
        $this->assertEquals('n-a', Jobeet::slugify(' - '));
        if (function_exists('iconv')) {
            $this->assertEquals('developpeur-web', Jobeet::slugify('Développeur Web'));
        }
    }

}
