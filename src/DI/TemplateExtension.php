<?php

declare(strict_types=1);

namespace Thunbolt\Templates\DI;

use Nette\DI\CompilerExtension;
use Thunbolt\Templates\Macros;
use Thunbolt\Templates\TemplateAdapter;
use Thunbolt\Templates\TemplateFactory;
use WebChemistry\Utils\DIHelpers;

class TemplateExtension extends CompilerExtension {

	public function loadConfiguration(): void {
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('templateAdapter'))
			->setType(TemplateAdapter::class);
	}

	public function beforeCompile(): void {
		$builder = $this->getContainerBuilder();
		$helpers = new DIHelpers($this->getContainerBuilder());

		$builder->getDefinition('latte.templateFactory')
			->addSetup('?->onCreate[] = [?, "create"]', ['@self', $this->prefix('@templateAdapter')]);

		$helpers->registerLatteMacroLoader(Macros::class);
	}

}
