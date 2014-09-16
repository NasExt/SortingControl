<?php

/**
 * This file is part of the NasExt extensions of Nette Framework
 *
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace NasExt\Controls\DI;

use Nette\DI\CompilerExtension;

/**
 * SortingControlExtension
 *
 * @author Dusan Hudak
 */
class SortingControlExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('sortingControl'))
			->setImplement('\NasExt\Controls\ISortingControlFactory')
			->setClass('\NasExt\Controls\SortingControl')
			->setArguments(array(
				new \Nette\PhpGenerator\PhpLiteral('$columns'),
				new \Nette\PhpGenerator\PhpLiteral('$defaultColumn'),
				new \Nette\PhpGenerator\PhpLiteral('$defaultSort'),
			));
	}
}
