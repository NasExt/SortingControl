<?php

/**
 * This file is part of the NasExt extensions of Nette Framework
 *
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace NasExt\Controls;

/**
 * @author Dusan Hudak <admin@dusan-hudak.com>
 */
interface ISortingControlFactory
{

	/**
	 * @param array $columns list of urlColumnName => originalColumnName
	 * @param string $defaultColumn
	 * @param string $defaultSort
	 * @return \NasExt\Controls\SortingControl
	 */
	public function create(array $columns, $defaultColumn, $defaultSort);
}
