<?php

declare(strict_types=1);

namespace Thunbolt\Templates;

use Latte;

class Macros extends Latte\Macros\MacroSet {

	public static function install(Latte\Compiler $compiler): void {
		$set = new static($compiler);

		$set->addMacro('isAllowed', [$set, 'isAllowed'], '}');
	}

	public function isAllowed(Latte\MacroNode $node, Latte\PhpWriter $writer): string {
		if (preg_match('#^[\w]+:?[\w]*$#', $node->args)) {
			return $writer->write('if ($user->isAllowed(%node.word)) {');
		} else {
			return $writer->write('if ($user->isAllowed(%node.args)) {');
		}
	}

}
