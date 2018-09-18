<?php

declare(strict_types=1);

namespace Thunbolt\Templates\DI;

use Nette\DI\CompilerExtension;
use Thunbolt\Templates\TemplateAdapter;
use Thunbolt\Templates\TemplateFactory;

class TemplateExtension extends CompilerExtension {

	public function loadConfiguration(): void {
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('templateAdapter'))
			->setType(TemplateAdapter::class);
	}

	public function beforeCompile(): void {
		$builder = $this->getContainerBuilder();

		$builder->getDefinition('latte.templateFactory')
			->addSetup('?->onCreate[] = [?, "create"]', ['@self', $this->prefix('@templateAdapter')]);
	}

}
