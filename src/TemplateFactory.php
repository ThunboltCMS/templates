<?php

namespace Thunbolt\Templates;

use Kdyby\Translation\Latte\TranslateMacros;
use Kdyby\Translation\TemplateHelpers;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Control;
use Nette\Application\UI\ITemplate;
use Nette\Application\UI\ITemplateFactory;
use Nette;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\Application\UI;
use Thunbolt\ICustomLayout;
use WebChemistry\Assets\Manager;
use WebChemistry\Filter\Cache;
use WebChemistry\Images\AbstractStorage;
use WebChemistry\Images\ImageStorageException;
use WebChemistry\Parameters\Provider;

class TemplateFactory extends Nette\Bridges\ApplicationLatte\TemplateFactory implements ITemplateFactory {

	/** @var ILatteFactory */
	private $latteFactory;

	/** @var Nette\Http\IRequest */
	private $httpRequest;

	/** @var Nette\Http\IResponse */
	private $httpResponse;

	/** @var Nette\Security\User */
	private $user;

	/** @var Nette\Caching\IStorage */
	private $cacheStorage;

	/** @var string */
	private $appDir;

	/** @var \WebChemistry\Parameters\Provider */
	private $parametersProvider;

	/** @var AbstractStorage */
	private $imageStorage;

	/** @var \Kdyby\Translation\Translator */
	private $translator;

	/** @var Manager */
	private $assetsManager;

	public function __construct(ILatteFactory $latteFactory, Nette\Http\IRequest $httpRequest = NULL,
								Nette\Http\IResponse $httpResponse = NULL, Nette\Security\User $user = NULL,
								Nette\Caching\IStorage $cacheStorage = NULL, Provider $parametersProvider = NULL,
								AbstractStorage $imageStorage = NULL, Translator $translator = NULL,
								Manager $assetsManager = NULL)
	{
		parent::__construct($latteFactory, $httpRequest, $httpResponse, $user, $cacheStorage);
		$this->latteFactory = $latteFactory;
		$this->httpRequest = $httpRequest;
		$this->httpResponse = $httpResponse;
		$this->user = $user;
		$this->cacheStorage = $cacheStorage;
		$this->parametersProvider = $parametersProvider;
		$this->imageStorage = $imageStorage;
		$this->translator = $translator;
		$this->assetsManager = $assetsManager;
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
		$latte->addFilter('isImageExists', array($this, '_isImageExists'));
		$latte->addFilter('date', array($filters, 'date'));
		$latte->addFilter('number', array($filters, 'number'));

		// filter loaders
		$latte->addFilter(NULL, array($filters, 'load'));
		if ($this->translator) {
			$filter = new TemplateHelpers($this->translator);
			$filter->register($latte);
		}

		// macros
		Macros::install($latte->getCompiler());
		TranslateMacros::install($latte->getCompiler());
		$latte->addMacro('cacheFilter', new Cache());

		// own parameters
		if ($presenter instanceof ICustomLayout && ($path = $presenter->getComponentMacroDirPath()) != NULL) {
			\ComponentMacro::install($latte, $path);
		} else if ($presenter instanceof UI\Presenter && $this->appDir) {
			\ComponentMacro::install($latte, $this->appDir . '/layouts/components/' . lcfirst($presenter->names['module']));
		}

		$template->settings = $this->parametersProvider;
		$template->imageStorage = $this->imageStorage;
		$template->lang = $this->translator ? $this->translator->getLocale() : NULL;
		$template->assets = $this->assetsManager;
		$template->assetsPath = $template->basePath . '/ins-assets';

		return $template;
	}

	/**
	 * @param string $absoluteName
	 * @return bool
	 * @throws ImageStorageException
	 */
	public function _isImageExists($absoluteName) {
		return $this->imageStorage->get($absoluteName)->isExists();
	}

	/**
	 * @param string $appDir
	 * @return TemplateFactory
	 */
	public function setAppDir($appDir) {
		$this->appDir = $appDir;

		return $this;
	}
}
