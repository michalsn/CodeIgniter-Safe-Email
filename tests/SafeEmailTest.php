<?php

include_once('vendor/autoload.php');

use \Mockery as m;

class SafeEmailTest extends PHPUnit_Framework_TestCase {

	public $safe_email;

	public function setUp()
	{
		include_once('application/hooks/Safe_email.php');

		$this->safe_email             = new Safe_email();
		$this->safe_email->ci->output = m::mock('output');
	}

	protected function tearDown() {
		m::close();
	}

	public function test_safe_email()
	{
		$this->safe_email->ci->output->shouldReceive('get_content_type')->once()->andReturn('text/html');

		$content = file_get_contents(__DIR__.'/fixtures/file1.html');
		$this->safe_email->ci->output->shouldReceive('get_output')->once()->andReturn($content);
		$this->safe_email->ci->output->shouldReceive('set_output')->once();
		$this->safe_email->ci->output->shouldReceive('_display')->once();

		$result = $this->safe_email->initialize();

		$this->assertTrue($result, 'safe_email is ok');

		// load output
		$domobj = new DOMDocument;
		libxml_use_internal_errors(TRUE);

		$domobj->loadHTML($this->safe_email->output);
		$xpath = new DOMXPath($domobj);

		$elem = $xpath->query("//span[@data-class='ci-safe-email']");

		// check emails converted to span elements
		$this->assertEquals(5, $elem->length, 'span elements count is ok');
		
		// check attributes for 1 element
		$attributes = $elem->item(0)->attributes;
		$this->assertEquals('data-key', $attributes[0]->name, 'span 1 data-key');
		$this->assertEquals('data-cipher', $attributes[1]->name, 'span 1 data-cipher');
		$this->assertEquals('data-class', $attributes[2]->name, 'span 1 data-class');
		$this->assertEquals('data-link', $attributes[3]->name, 'span 1 data-link');
		$this->assertEquals('true', $attributes[3]->value, 'span 1 data-link value');
		$this->assertEquals('data-content', $attributes[4]->name, 'span 1 data-content');
		$this->assertEquals('email', $attributes[4]->value, 'span 1 data-content value');

		// check attributes for 2 element
		$attributes = $elem->item(1)->attributes;
		$this->assertEquals('data-key', $attributes[0]->name, 'span 2 data-key');
		$this->assertEquals('data-cipher', $attributes[1]->name, 'span 2 data-cipher');
		$this->assertEquals('data-class', $attributes[2]->name, 'span 2 data-class');
		$this->assertEquals('data-link', $attributes[3]->name, 'span 2 data-link');
		$this->assertEquals('true', $attributes[3]->value, 'span 2 data-link value');
		$this->assertEquals('data-attr', $attributes[4]->name, 'span 2 data-attr');
		$this->assertEquals('class="sample"', $attributes[4]->value, 'span 2 data-attr value');

		// check attributes for 3 element
		$attributes = $elem->item(2)->attributes;
		$this->assertEquals('data-key', $attributes[0]->name, 'span 3 data-key');
		$this->assertEquals('data-cipher', $attributes[1]->name, 'span 3 data-cipher');
		$this->assertEquals('data-class', $attributes[2]->name, 'span 3 data-class');
		$this->assertEquals('data-link', $attributes[3]->name, 'span 3 data-link');
		$this->assertEquals('true', $attributes[3]->value, 'span 3 data-link value');
		$this->assertEquals('data-content', $attributes[4]->name, 'span 3 data-content');
		$this->assertEquals('<img height="23" width="23" title="Title" src="image1.png">', $attributes[4]->value, 'span 3 data-content value');

		// check attributes for 4 element
		$attributes = $elem->item(3)->attributes;
		$this->assertEquals('data-key', $attributes[0]->name, 'span 4 data-key');
		$this->assertEquals('data-cipher', $attributes[1]->name, 'span 4 data-cipher');
		$this->assertEquals('data-class', $attributes[2]->name, 'span 4 data-class');
		$this->assertEquals('data-link', $attributes[3]->name, 'span 4 data-link');
		$this->assertEquals('false', $attributes[3]->value, 'span 4 data-link value');

		// check attributes for 4 element
		$attributes = $elem->item(4)->attributes;
		$this->assertEquals('data-key', $attributes[0]->name, 'span 5 data-key');
		$this->assertEquals('data-cipher', $attributes[1]->name, 'span 5 data-cipher');
		$this->assertEquals('data-class', $attributes[2]->name, 'span 5 data-class');
		$this->assertEquals('data-link', $attributes[3]->name, 'span 5 data-link');
		$this->assertEquals('true', $attributes[3]->value, 'span 5 data-link value');
		$this->assertEquals('data-content', $attributes[4]->name, 'span 5 data-content');
		$this->assertEquals('<img height="23" width="23" title="'.$attributes[0]->value.'" src="image1.png"> this is '.$attributes[0]->value, $attributes[4]->value, 'span 5 data-content value');
		$this->assertEquals('data-attr', $attributes[5]->name, 'span 5 data-attr');
		$this->assertEquals('data-id="sample" class="something"  data-else="test"', $attributes[5]->value, 'span 5 data-attr value');
	}

	public function test_safe_email_fail()
	{
		$this->safe_email->ci->output->shouldReceive('get_content_type')->once()->andReturn('application/json');

		$result = $this->safe_email->initialize();

		$this->assertFalse($result, 'safe_email is false');
	}

}