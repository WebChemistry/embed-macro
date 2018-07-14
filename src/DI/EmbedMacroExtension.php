<?php declare(strict_types = 1);

namespace WebChemistry\Macros\DI;

use Nette\DI\CompilerExtension;
use WebChemistry\Macros\EmbedMacro;

class EmbedMacroExtension extends CompilerExtension {

	/** @var string */
	private const MACRO = EmbedMacro::class . '::install';

	public function beforeCompile(): void {
		$builder = $this->getContainerBuilder();

		$builder->getDefinition('latte.latteFactory')
			->addSetup('?->onCompile[] = function ($engine) { ' . self::MACRO . '($engine->getCompiler()); }', ['@self']);
	}

}
