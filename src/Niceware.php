<?php
namespace Narf\Niceware;

class Niceware {

	const MAX_PASSPHRASE_SIZE = 1024;

	private static $wordList;

	/**
	 * @codeCoverageIgnore
	 */
	private static function loadWordList()
	{
		static::$wordList = require_once __DIR__.DIRECTORY_SEPARATOR.'word-list.php';

		if ( ! \is_array(static::$wordList))
		{
			static::$wordList = null;
			throw new \RuntimeException("Unable to load wordlist");
		}

		if (count(static::$wordList) !== 65536)
		{
			throw new \RuntimeException("Wordlist is invalid");
		}

		$iterator = 0;
		\array_walk(static::$wordList, function(&$word, $index) use (&$iterator) {
			if ($index !== $iterator++ || ! \is_string($word) || ! isset($word[0]))
			{
				throw new \RuntimeException("Wordlist is invalid");
			}
		});
	}

	/**
	 * @param	string	$bytes
	 */
	public static function bytesToPassphrase($bytes): string
	{
		if ( ! \is_string($bytes))
		{
			$type = \gettype($bytes);
			throw new \InvalidArgumentException("Input must be a string; {$type} given}");
		}

		$bytesLength = \function_exists('mb_strlen') ? \mb_strlen($bytes, '8bit') : \strlen($bytes);
		if ($bytesLength % 2 !== 0)
		{
			throw new \InvalidArgumentException("Input length must be even-sized");
		}
		elseif ($bytesLength === 0)
		{
			return '';
		}

		isset(static::$wordList) || self::loadWordList();

		$words = [];
		for ($byteIndex = 0; $byteIndex < $bytesLength; $byteIndex += 2)
		{
			list(, $byte, $next) = \unpack('C2', $bytes[$byteIndex].$bytes[$byteIndex + 1]);
			$wordIndex           = $byte * 256 + $next;

			if ( ! isset(static::$wordList[$wordIndex]))
			{
				// @codeCoverageIgnoreStart
				throw new \UnexpectedValueException("Invalid byte encountered");
				// @codeCoverageIgnoreEnd
			}

			$words[] = static::$wordList[$wordIndex];
		}

		return \implode(' ', $words);
	}

	/**
	 * @param	string	$passphrase
	 */
	public static function passphraseToBytes($passphrase): string
	{
		if ( ! \is_string($passphrase))
		{
			$type = \gettype($passphrase);
			throw new \InvalidArgumentException("Input must be a string; {$type} given}");
		}
		elseif ($passphrase === '')
		{
			return '';
		}

		$words = \explode(' ', $passphrase);
		if (isset($words[self::MAX_PASSPHRASE_SIZE / 2]))
		{
			throw new \Exception("Invalid passphrase");
		}

		isset(static::$wordList) || self::loadWordList();

		$bytes = '';
		foreach ($words as &$word)
		{
			$word = \function_exists('mb_strtolower') ? \mb_strtolower($word, 'ascii') : \strtolower($word);
			if ( ! isset($word[0]) || false === ($wordIndex = \array_search($word, static::$wordList, true)))
			{
				throw new \Exception("Invalid passphrase");
			}

			$bytes .= \pack('CC', (int) \floor($wordIndex / 256), $wordIndex % 256);
		}

		return $bytes;
	}

	/**
	 * @param	int	$size
	 */
	public static function generatePassphrase($size): string
	{
		if ( ! \is_int($size))
		{
			$type = \gettype($size);
			throw new \InvalidArgumentException("Size must be an integer; {$type} given");
		}
		elseif ($size === 0)
		{
			// I don't really like accepting 0, but the original package does it ...
			return '';
		}
		elseif ($size < 0 || $size > self::MAX_PASSPHRASE_SIZE || $size % 2 !== 0)
		{
			throw new \InvalidArgumentException("Size must be an even number between 0 and 1024 bytes");
		}

		$bytes = \random_bytes($size);
		return self::bytesToPassphrase($bytes);
	}
}
