<?php

namespace Thunbolt\Templates\DI;

use Nette\DI\CompilerExtension;

class TemplateExtension extends CompilerExtension {

	public function loadConfiguration() {
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('templateCache'))
			->setClass('Thunbolt\Template\TemplateCache');
	}

	public function beforeCompile() {
		$builder = $this->getContainerBuilder();

		$builder->getDefinition('latte.templateFactory')
			->setClass('Thunbolt\Template\TemplateFactory')
			->addSetup('setAppDir', $builder->parameters['appDir']);
	}

}
