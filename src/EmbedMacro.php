<?php declare(strict_types = 1);

namespace WebChemistry\Macros;

use Latte\CompileException;
use Latte\Compiler;
use Latte\Helpers;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;

class EmbedMacro extends MacroSet {

	public static function install(Compiler $compiler): void {
		$me = new static($compiler);

		$me->addMacro('embed', [$me, 'embedBegin'], [$me, 'embedEnd']);
	}

	public function embedBegin(): string {
		return 'ob_start();';
	}

	public function embedEnd(MacroNode $node, PhpWriter $writer) {
		$node->replaced = false;
		$destination = $node->tokenizer->fetchWord(); // destination [,] [params]
		if (!preg_match('~#|[\w-]+\z~A', $destination)) {
			return false;
		}

		$destination = ltrim($destination, '#');
		$parent = $destination === 'parent';
		if ($destination === 'parent' || $destination === 'this') {
			for ($item = $node->parentNode; $item && $item->name !== 'block' && !isset($item->data->name); $item = $item->parentNode);
			if (!$item) {
				throw new CompileException("Cannot include $destination block outside of any block.");
			}
			$destination = $item->data->name;
		}

		$noEscape = Helpers::removeFilter($node->modifiers, 'noescape');
		if (!$noEscape && Helpers::removeFilter($node->modifiers, 'escape')) {
			trigger_error('Macro ' . $node->getNotation() . ' provides auto-escaping, remove |escape.');
		}
		if ($node->modifiers && !$noEscape) {
			$node->modifiers .= '|escape';
		}
		return $writer->write(
			'$_tmp = trim(ob_get_clean());'
			. '$this->renderBlock' . ($parent ? 'Parent' : '') . '('
			. (strpos($destination, '$') === false ? var_export($destination, true) : $destination)
			. ', ["_content" => $_tmp] + %node.array? + '
			. (isset($this->namedBlocks[$destination]) || $parent ? 'get_defined_vars()' : '$this->params')
			. ($node->modifiers
				? ', function ($s, $type) { $_fi = new LR\FilterInfo($type); return %modifyContent($s); }'
				: ($noEscape || $parent ? '' : ', ' . var_export(implode($node->context), true)))
			. ');'
		);
	}

}
