<?php
namespace Narf\Niceware\Tests;

use PHPUnit\Framework\TestCase;
use Narf\Niceware\Niceware;

class GeneratePassphraseTest extends TestCase {

	/**
	 * @dataProvider	createValidSizes
	 */
	public function testValid($inputSize, $resultWordCount)
	{
		$generatedWords = Niceware::generatePassphrase($inputSize);
		$this->assertSame(
			$resultWordCount,
			\str_word_count(Niceware::generatePassphrase($inputSize))
		);
	}

	public function createValidSizes()
	{
		return [
			[2,   1],
			[0,   0],
			[20,  10],
			[512, 256]
		];
	}

	/**
	 * @dataProvider	createInvalidSizes
	 * @expectedException	InvalidArgumentException
	 */
	public function testInvalid($inputSize)
	{
		Niceware::generatePassphrase($inputSize);
	}

	public function createInvalidSizes()
	{
		return [
			'Non-integer'                    => ['string'],
			'Non-integer, but numeric'       => ['1'],
			'Integer, but less than 0'       => [-4],
			'Integer, but greater than 1024' => [1026],
			'Integer, but not even'          => [1],
			'Another odd integer'            => [23]
		];
	}
}
