<?php

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
use Thunbolt\Config\Config;
use Thunbolt\Localization\TranslatorProvider;
use WebChemistry\Assets\AssetsManager;
use WebChemistry\Images\IImageStorage;
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

	/** @var IImageStorage */
	private $imageStorage;

	/** @var ITranslator */
	private $translator;

	/** @var AssetsManager */
	private $assetsManager;

	/** @var callable[] */
	public $onCreate = [];

	/** @var Config */
	private $config;

	public function __construct(ILatteFactory $latteFactory, IRequest $httpRequest = NULL,
								IResponse $httpResponse = NULL, User $user = NULL,
								IStorage $cacheStorage = NULL, Config $config = NULL,
								IImageStorage $imageStorage = NULL, TranslatorProvider $translatorProvider = NULL,
								AssetsManager $assetsManager = NULL)
	{
		parent::__construct($latteFactory, $httpRequest, $user, $cacheStorage);
		$this->latteFactory = $latteFactory;
		$this->httpRequest = $httpRequest;
		$this->httpResponse = $httpResponse;
		$this->user = $user;
		$this->cacheStorage = $cacheStorage;
		$this->imageStorage = $imageStorage;
		$this->translator = $translatorProvider->getTranslator();
		$this->assetsManager = $assetsManager;
		$this->config = $config;
	}

	/**
	 * @return ITemplate
	 */
	public function createTemplate(Control $control = NULL) {
		$template = parent::createTemplate($control);
		$latte = $template->getLatte();
		$presenter = $control ? $control->getPresenter(FALSE) : NULL;

		// filters
		$filters = new Filters();
		$latte->addFilter('date', array($filters, 'date'));
		$latte->addFilter('number', array($filters, 'number'));

		// filter loaders
		$latte->addFilter(NULL, array($filters, 'load'));

		// macros
		Macros::install($latte->getCompiler());

		if (class_exists(ComponentMacro::class) && $control instanceof IPresenter) {
			if (($ctrl = $control) instanceof ICustomComponentMacro || ($ctrl = $presenter) instanceof ICustomComponentMacro) {
				if (($path = $ctrl->getComponentMacroDirectory()) !== NULL) {
					ComponentMacro::install($latte->getCompiler(), $path);
				}
			} else if ($presenter instanceof IPresenter && $this->appDir) {
				ComponentMacro::install($latte->getCompiler(), $this->appDir . '/layouts/components/' . lcfirst($presenter->names['module']));
			}
		}

		// parameters
		$template->setTranslator($this->translator);
		if ($this->config) {
			$template->config = $this->config->getValues();
		}
		$template->imageStorage = $this->imageStorage;
		$template->assets = $this->assetsManager;
		if (class_exists(ComposerDirectories::class)) {
			$template->plgPath = $template->basePath . '/' . ComposerDirectories::PLG_RES_REL_DIR;
		}

		foreach ($this->onCreate as $callback) {
			$callback($template, $control);
		}

		return $template;
	}

	/**
	 * @param string $appDir
	 */
	public function setDirectories($appDir) {
		$this->appDir = $appDir;
	}

}
