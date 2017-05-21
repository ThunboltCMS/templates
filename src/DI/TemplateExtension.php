<?php

declare(strict_types=1);

namespace Thunbolt\Templates\DI;

use Nette\DI\CompilerExtension;
use Thunbolt\Templates\TemplateFactory;

class TemplateExtension extends CompilerExtension {

	public function beforeCompile(): void {
		$builder = $this->getContainerBuilder();

		$builder->getDefinition('latte.templateFactory')
			->setFactory(TemplateFactory::class)
			->addSetup('setDirectories', [$builder->parameters['appDir']]);
	}

}
