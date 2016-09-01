<?php

namespace Thunbolt\Templates\DI;

use Nette\DI\CompilerExtension;
use Thunbolt\Templates\TemplateFactory;

class TemplateExtension extends CompilerExtension {

	public function beforeCompile() {
		$builder = $this->getContainerBuilder();

		$builder->getDefinition('latte.templateFactory')
			->setFactory(TemplateFactory::class)
			->addSetup('setDirectories', [$builder->parameters['appDir']]);
	}

}
