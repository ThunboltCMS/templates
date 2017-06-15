<?php

declare(strict_types=1);

namespace Thunbolt\Templates;

use Nette\Application\IPresenter;
use Nette\Caching\IStorage;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Localization\ITranslator;
use Nette\Application\UI\Control;
use Nette\Application\UI\ITemplate;
use Nette\Application\UI\ITemplateFactory;
use Nette\Bridges\ApplicationLatte;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\Security\User;
use Thunbolt\Composer\ComposerDirectories;
use Thunbolt\Config\IConfig;
use WebChemistry\Assets\AssetsManager;
use WebChemistry\Macros\ComponentMacro;

class TemplateFactory extends ApplicationLatte\TemplateFactory implements ITemplateFactory {

	/** @var ILatteFactory */
	private $latteFactory;

	/** @var IRequest */
	private $httpRequest;

	/** @var IResponse */
	private $httpResponse;

	/** @var User */
	private $user;

	/** @var IStorage */
	private $cacheStorage;

	/** @var string */
	private $appDir;

	/** @var ITranslator */
	private $translator;

	/** @var AssetsManager */
	private $assetsManager;

	/** @var callable[] */
	public $onCreate = [];

	/** @var IConfig */
	private $config;

	public function __construct(ILatteFactory $latteFactory, IRequest $httpRequest = NULL,
								IResponse $httpResponse = NULL, User $user = NULL,
								IStorage $cacheStorage = NULL, IConfig $config = NULL, ITranslator $translator = NULL,
								AssetsManager $assetsManager = NULL)
	{
		parent::__construct($latteFactory, $httpRequest, $user, $cacheStorage);

		$this->latteFactory = $latteFactory;
		$this->httpRequest = $httpRequest;
		$this->httpResponse = $httpResponse;
		$this->user = $user;
		$this->cacheStorage = $cacheStorage;
		$this->translator = $translator;
		$this->assetsManager = $assetsManager;
		$this->config = $config;
	}

	/**
	 * @return ITemplate
	 */
	public function createTemplate(Control $control = NULL): ITemplate {
		$template = parent::createTemplate($control);
		$latte = $template->getLatte();
		$presenter = $control ? $control->getPresenter(FALSE) : NULL;

		// macros
		Macros::install($latte->getCompiler());

		if ($this->appDir && class_exists(ComponentMacro::class) && $presenter instanceof IPresenter) {
			if ($presenter instanceof ICustomComponentMacro) {
				if (($path = $presenter->getComponentMacroDirectory()) !== NULL) {
					ComponentMacro::install($latte->getCompiler(), [$this->appDir . '/layouts/components/' . lcfirst($presenter->names['module']), $path]);
				}
			} else {
				ComponentMacro::install($latte->getCompiler(), [$this->appDir . '/layouts/components/' . lcfirst($presenter->names['module'])]);
			}
		}

		// parameters
		$template->setTranslator($this->translator);
		if ($this->config) {
			$template->config = $this->config->getValues();
		}

		$latte->addProvider('assetsManager', $this->assetsManager);
		if (class_exists(ComposerDirectories::class)) {
			$template->pluginPath = $template->basePath . '/' . ComposerDirectories::PLUGIN_ASSETS_DIR;
		}

		foreach ($this->onCreate as $callback) {
			$callback($template, $control);
		}

		return $template;
	}

	public function setDirectories(string $appDir): void {
		$this->appDir = $appDir;
	}

}
