<?php

namespace Thunbolt\Templates;

use Latte;

class Macros extends Latte\Macros\MacroSet {

	/**
	 * @param Latte\Compiler $compiler
	 */
	public static function install(Latte\Compiler $compiler) {
		$set = new static($compiler);

		$set->addMacro('isAllowed', [$set, 'isAllowed'], '}');
	}

	/**
	 * @param Latte\MacroNode $node
	 * @param Latte\PhpWriter $writer
	 * @return string
	 */
	public function isAllowed(Latte\MacroNode $node, Latte\PhpWriter $writer) {
		if (preg_match('#^[\w]+:?[\w]*$#', $node->args)) {
			return $writer->write('if ($user->isAllowed(%node.word)) {');
		} else {
			return $writer->write('if ($user->isAllowed(%node.args)) {');
		}
	}

}
