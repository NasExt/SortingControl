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

use Kdyby\Autowired\InvalidArgumentException;
use Nette\Application\UI\Control;

/**
 * @author Dusan Hudak <admin@dusan-hudak.com>
 */
class SortingControl extends Control
{
	const ASC = 'asc';
	const DESC = 'desc';

	/** @persistent */
	public $column;

	/** @persistent */
	public $sort;

	/** @var  string */
	private $defaultColumn;

	/** @var  string */
	private $defaultSort;

	/** @var array */
	private $columns = array();

	/** @var  bool */
	private $ajaxRequest;

	/** @var array */
	public $onShort;

	/** @var  string */
	private $templateFile;


	/**
	 * @param array $columns list of urlColumnName => originalColumnName
	 * @param string $defaultColumn
	 * @param string $defaultSort
	 */
	public function __construct(array $columns, $defaultColumn, $defaultSort)
	{
		parent::__construct();

		$reflection = $this->getReflection();
		$dir = dirname($reflection->getFileName());
		$name = $reflection->getShortName();
		$this->templateFile = $dir . DIRECTORY_SEPARATOR . $name . '.latte';

		if ($defaultSort !== self::ASC || $defaultSort !== self::DESC) {
			new InvalidArgumentException('Parameter "' . $defaultSort . '" must be value of SortingControl::ASC or SortingControl::DESC!');
		}
		$this->defaultColumn = $defaultColumn;
		$this->defaultSort = $defaultSort;
		$this->columns = $columns;

		if ($this->column == NULL) {
			$this->column = $this->defaultColumn;
		}
		if ($this->sort == NULL) {
			$this->sort = $this->defaultSort;
		}
	}


	/**
	 * @return string
	 */
	public function getSortDirection()
	{
		if ($this->sort === self::ASC || $this->sort === self::DESC) {
			return strtoupper($this->sort);
		}

		return strtoupper($this->defaultSort);
	}


	/**
	 * @return string|array
	 */
	public function getColumn()
	{
		if (isset($this->columns[$this->column])) {
			return $this->columns[$this->column];
		}

		return $this->defaultColumn;
	}


	/**
	 * @return array
	 */
	public function getSort()
	{
		$sort = array();

		if (is_array($this->getColumn())) {
			foreach ($this->getColumn() as $column) {
				$sort[] = $column . ' ' . $this->getSortDirection();
			}
		} else {
			$sort[] = $this->getColumn() . ' ' . $this->getSortDirection();
		}

		return $sort;
	}


	/**
	 * @param bool $value
	 * @return SortingControl
	 */
	public function setAjaxRequest($value = TRUE)
	{
		$this->ajaxRequest = $value;
		return $this;
	}


	/**
	 * @param string $column
	 * @param string $sort
	 */
	public function handleSort($column, $sort)
	{
		if (isset($this->columns[$column])) {
			$this->column = $column;
		} else {
			$this->column = $this->defaultColumn;
		}

		if ($sort === self::ASC || $sort === self::DESC) {
			$this->sort = $sort;
		} else {
			$this->sort = $this->defaultSort;
		}

		$this->onShort($this);
	}


	/**
	 * @return string
	 */
	public function getTemplateFile()
	{
		return $this->templateFile;
	}


	/**
	 * @param string $file
	 * @return SortingControl
	 */
	public function setTemplateFile($file)
	{
		if ($file) {
			$this->templateFile = $file;
		}
		return $this;
	}


	/**
	 * @param string $column
	 * @param null|string $title
	 */
	public function render($column, $title = NULL)
	{
		$linkParams['column'] = $column;
		$linkParams['sort'] = self::ASC;
		$sort = '';

		if ($column == $this->column) {
			$linkParams['sort'] = $this->sort == self::ASC ? self::DESC : self::ASC;

			if ($this->sort == self::ASC) {
				$sort = self::ASC;
			}
			if ($this->sort == self::DESC) {
				$sort = self::DESC;
			}
		}
		$url = $this->link('sort!', $linkParams);

		$template = $this->template;
		$template->url = $url;
		$template->ajaxRequest = $this->ajaxRequest;
		$template->title = $title ? $title : $column;
		$template->sort = $sort;

		$template->setFile($this->getTemplateFile());
		$template->render();
	}
}
