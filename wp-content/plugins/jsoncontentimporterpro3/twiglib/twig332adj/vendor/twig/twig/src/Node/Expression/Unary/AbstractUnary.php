<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 * (c) Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JCITwig\Node\Expression\Unary;

use JCITwig\Compiler;
use JCITwig\Node\Expression\AbstractExpression;
use JCITwig\Node\Node;

abstract class AbstractUnary extends AbstractExpression
{
    public function __construct(Node $node, int $lineno)
    {
        parent::__construct(['node' => $node], [], $lineno);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler->raw(' ');
        $this->operator($compiler);
        $compiler->subcompile($this->getNode('node'));
    }

    abstract public function operator(Compiler $compiler): Compiler;
}
