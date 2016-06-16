<?php

namespace Thunbolt\Templates;

use Kdyby\Translation\Latte\TranslateMacros;
use Kdyby\Translation\TemplateHelpers;
use Kdyby\Translation\Translator;
use Nette\Caching\IStorage;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Localization\ITranslator;
use Nette\Application\UI\Control;
use Nette\Application\UI\ITemplate;
use Nette\Application\UI\ITemplateFactory;
use Nette\Bridges\ApplicationLatte;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\Application\UI;
use Nette\Security\User;
use Thunbolt\Translation\TranslationMediator;
use WebChemistry\Assets\Manager;
use WebChemistry\Filter\Cache;
use WebChemistry\Images\AbstractStorage;
use WebChemistry\Images\ImageStorageException;
use WebChemistry\Macros\ComponentMacro;
use WebChemistry\Parameters\Provider;

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

	/** @var Provider */
	private $parametersProvider;

	/** @var AbstractStorage */
	private $imageStorage;

	/** @var ITranslator */
	private $translator;

	/** @var Manager */
	private $assetsManager;

	public function __construct(ILatteFactory $latteFactory, IRequest $httpRequest = NULL,
								IResponse $httpResponse = NULL, User $user = NULL,
								IStorage $cacheStorage = NULL, Provider $parametersProvider = NULL,
								AbstractStorage $imageStorage = NULL, ITranslator $translator = NULL,
								Manager $assetsManager = NULL)
	{
		parent::__construct($latteFactory, $httpRequest, $user, $cacheStorage);
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
		if (!$this->translator) {
			$latte->addFilter('translate', function ($s) { // void translator
				return $s;
			});
		}

		// macros
		Macros::install($latte->getCompiler());
		$latte->addMacro('cacheFilter', new Cache());

		if ($presenter instanceof ICustomComponentMacro && ($path = $presenter->getComponentMacroDirectory()) != NULL) {
			ComponentMacro::install($latte->getCompiler(), $path);
		} else if ($presenter instanceof UI\Presenter && $this->appDir) {
			ComponentMacro::install($latte->getCompiler(), $this->appDir . '/layouts/components/' . lcfirst($presenter->names['module']));
		} else {
			ComponentMacro::install($latte->getCompiler(), dirname($control->getReflection()->getFileName()) . '/components');
		}

		// parameters
		$template->settings = $this->parametersProvider;
		$template->imageStorage = $this->imageStorage;
		$template->lang = new TranslationMediator($this->translator);
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
