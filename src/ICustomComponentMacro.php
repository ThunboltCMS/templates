<?php

declare(strict_types=1);

namespace Thunbolt\Templates;

interface ICustomComponentMacro {

	/**
	 * Returns path to base directory for components
	 *
	 * @return string
	 */
	public function getComponentMacroDirectory(): string;

}
