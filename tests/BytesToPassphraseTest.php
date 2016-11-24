<?php
namespace Narf\Niceware\Tests;

use PHPUnit\Framework\TestCase;
use Narf\Niceware\Niceware;

class BytesToPassphraseTest extends TestCase {

	/**
	 * @dataProvider	createValidBytes
	 */
	public function testValid($inputBytes, $expectedPassphrase)
	{
		$resultPassphrase = Niceware::bytesToPassphrase($inputBytes);
		$this->assertSame($expectedPassphrase, $resultPassphrase);
	}

	public function createValidBytes()
	{
		return [
			["",         ""],
			["\x00\x00", "a"],
			["\xff\xff", "zyzzyva"],
			[
				"\x00\x00\x11\xd4\x0c\x8c\x5a\xf7\x2e\x53\xfe\x3c\x36\xa9\xff\xff",
				"a bioengineering balloted gobbledegook creneled written depriving zyzzyva"
			]
		];
	}

	/**
	 * @dataProvider	createInvalidBytes
	 * @expectedException	InvalidArgumentException
	 */
	public function testInvalid($inputBytes)
	{
		Niceware::bytesToPassphrase($inputBytes);
	}

	public function createInvalidBytes()
	{
		return [
			['Odd length'   => "\x01"],
			['Not a string' => 2]
		];
	}
}
