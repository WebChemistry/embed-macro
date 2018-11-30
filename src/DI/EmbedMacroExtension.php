<?php declare(strict_types = 1);

namespace WebChemistry\Macros\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;
use WebChemistry\Macros\EmbedMacro;

class EmbedMacroExtension extends CompilerExtension {

	/** @var string */
	private const MACRO = EmbedMacro::class . '::install';

	public function beforeCompile(): void {
		$builder = $this->getContainerBuilder();

		$def = $builder->getDefinition('latte.latteFactory');
		if ($def instanceof FactoryDefinition) {
			$def = $def->getResultDefinition();
		}
		$def->addSetup('?->onCompile[] = function ($engine) { ' . self::MACRO . '($engine->getCompiler()); }', ['@self']);
	}

}
