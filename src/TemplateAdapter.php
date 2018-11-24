<?php declare(strict_types = 1);

namespace Thunbolt\Templates;

use Nette\Application\UI\ITemplate;
use Nette\Localization\ITranslator;

class TemplateAdapter {

	/** @var ITranslator|null */
	private $translator;

	public function __construct(?ITranslator $translator = null) {
		$this->translator = $translator;
	}

	public function create(ITemplate $template): void {
		if ($this->translator) {
			$template->setTranslator($this->translator);
		}

		if (class_exists(ComposerDirectories::class)) {
			$template->pluginPath = $template->basePath . '/' . ComposerDirectories::PLUGIN_ASSETS_DIR;
		}
	}

}
