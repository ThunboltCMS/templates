<?php

namespace Thunbolt\Templates;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Object;

class TemplateCache extends Object {

	/** @var \Nette\Caching\Cache */
	private $cache;

	/**
	 * @param IStorage $storage
	 */
	public function __construct(IStorage $storage) {
		$this->cache = new Cache($storage, '_Nette.Templating.Cache');
	}

	/**
	 * @return \Nette\Caching\Cache
	 */
	public function getCache() {
		return $this->cache;
	}

	/**
	 * @param array|string $tags
	 */
	public function cleanTags($tags) {
		$this->cache->clean([
			Cache::TAGS => (array) $tags
		]);
	}

}
