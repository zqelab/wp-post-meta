<?php
use PHPUnit\Framework\TestCase;
use Zqe\Wp_Post_Meta;

class WpPostMetaTest extends TestCase {
    public function testClassInstantiation() {
        $instance = new Wp_Post_Meta('post', []);
        $this->assertInstanceOf(Wp_Post_Meta::class, $instance);
    }
}
