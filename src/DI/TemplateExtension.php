<?php

namespace Thunbolt\Templates\DI;

use Nette\DI\CompilerExtension;

class TemplateExtension extends CompilerExtension {

	public function loadConfiguration() {
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('templateCache'))
			->setClass('Thunbolt\Templates\TemplateCache');
	}

	public function beforeCompile() {
		$builder = $this->getContainerBuilder();

		$builder->getDefinition('latte.templateFactory')
			->setFactory('Thunbolt\Templates\TemplateFactory')
			->addSetup('setAppDir', [$builder->parameters['appDir']]);
	}

}
