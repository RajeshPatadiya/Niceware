<?php
namespace Narf\Niceware\Tests;

use PHPUnit\Framework\TestCase;
use Narf\Niceware\Niceware;

class PassphraseToBytesTest extends TestCase {

	/**
	 * @dataProvider	createValidPassphrases
	 */
	public function testValid($inputPassphrase, $expectedBytes)
	{
		$resultBytes = Niceware::passphraseToBytes($inputPassphrase);
		$this->assertSame(\bin2hex($expectedBytes), \bin2hex($resultBytes));
	}

	public function createValidPassphrases()
	{
		return [
			["",        ""],
			["A",       "\x00\x00"],
			["zyzzyva", "\xff\xff"],
			[
				"A bioengineering Balloted gobbledegooK cReneled Written depriving zyzzyva",
				"\x00\x00\x11\xd4\x0c\x8c\x5a\xf7\x2e\x53\xfe\x3c\x36\xa9\xff\xff",
			]
		];
	}

	/**
	 * @expectedException	InvalidArgumentException
	 */
	public function testInvalidInputTypes()
	{
		Niceware::passphraseToBytes(1);
	}

	/**
	 * @dataProvider		createInvalidPassphrases
	 * @expectedException		Exception
	 * @expectedExceptionMessage	Invalid passphrase
	 */
	public function testInvalidPassphrases($passphrase)
	{
		Niceware::passphraseToBytes($passphrase);
	}

	public function createInvalidPassphrases()
	{
		return [
			'Word not in wordlist'   => ["i love ninetales"],
			'Space first'            => [" zyzzyva"],
			'2 spaces in the middle' => ["zyzzyva  zyzzyva"],
			'Space last'             => ["zyzzyva "],
			'Exceeds size limit'     => ["a".\str_repeat(" a", 512)]
		];
	}
}
