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

use NasExt\Controls\Exception\InvalidArgumentException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Utils\Json;

/**
 * @author Dusan Hudak <admin@dusan-hudak.com>
 */
class SortingControl extends Control
{
	const ASC = 'asc';
	const DESC = 'desc';
	const MASK_PREFIX = 'sort-';
	const COLUMN_NAME = 'column';
	const DIRECTION_NAME = 'direction';

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
	public $templateFile;

	/** @var string */
	private $cookieMask;

	/** @var  bool */
	private $saveSorting;

	/** @var  IRequest */
	private $httpRequest;

	/** @var  IResponse */
	private $httpResponse;


	/**
	 * @param array $columns list of urlColumnName => originalColumnName
	 * @param string $defaultColumn
	 * @param string $defaultSort
	 * @param IRequest $httpRequest
	 * @param IResponse $httpResponse
	 */
	public function __construct(array $columns, $defaultColumn, $defaultSort, IRequest $httpRequest, IResponse $httpResponse)
	{
		parent::__construct();

		$this->httpRequest = $httpRequest;
		$this->httpResponse = $httpResponse;

		$reflection = $this->getReflection();
		$dir = dirname($reflection->getFileName());
		$name = $reflection->getShortName();
		$this->templateFile = $dir . DIRECTORY_SEPARATOR . $name . '.latte';

		$this->validateDefaultSort($defaultSort);

		$this->defaultColumn = $defaultColumn;
		$this->defaultSort = $defaultSort;
		$this->columns = $columns;
	}


	/**
	 * This method will be called when the component (or component's parent)
	 * becomes attached to a monitored object. Do not call this method yourself.
	 * @param  Nette\ComponentModel\IComponent
	 * @return void
	 */
	protected function attached($presenter)
	{
		if ($presenter instanceof Presenter) {
			$this->cookieMask = self::MASK_PREFIX . $this->presenter->name . ":" . $this->name;

			// if set saveSorting set defaults from cookie
			if ($this->saveSorting == TRUE) {
				$value = $this->httpRequest->getCookie($this->cookieMask);
				if (!empty($value)) {
					$value = Json::decode($value, Json::FORCE_ARRAY);
					if (($value[self::DIRECTION_NAME] == self::ASC || $value[self::DIRECTION_NAME] == self::DESC) &&
						isset($this->columns[$value[self::COLUMN_NAME]])
					) {
						$this->defaultColumn = $value[self::COLUMN_NAME];
						$this->defaultSort = $value[self::DIRECTION_NAME];
					}
				}
			}

			if ($this->column == NULL) {
				$this->column = $this->defaultColumn;
			}
			if ($this->sort == NULL) {
				$this->sort = $this->defaultSort;
			}
		}
		parent::attached($presenter);
	}


	/**
	 * @param string $defaultSort
	 */
	private function validateDefaultSort($defaultSort)
	{
		if ($defaultSort !== self::ASC || $defaultSort !== self::DESC) {
			new InvalidArgumentException('Parameter "' . $defaultSort . '" must be value of SortingControl::ASC or SortingControl::DESC!');
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
	 * @param bool $value
	 * @return SortingControl
	 */
	public function setSaveSorting($value = TRUE)
	{
		$this->saveSorting = $value;
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

		// save sorting
		if ($this->saveSorting == TRUE) {
			$data = array(
				self::COLUMN_NAME => $this->column,
				self::DIRECTION_NAME => $this->sort,
			);
			$this->httpResponse->setCookie($this->cookieMask, Json::encode($data), 0);
		}


		$this->onShort($this);
	}


	/**
	 * @param string $column
	 * @param null|string $title
	 * @param null|string $direction
	 */
	public function render($column, $title = NULL, $direction = NULL)
	{
		$linkParams['column'] = $column;
		$linkParams['sort'] = self::ASC;
		$sort = '';
		$active = FALSE;

		if ($column == $this->column) {
			if ($direction) {
				$linkParams['sort'] = $sort = $direction;

				if ($linkParams['sort'] == $this->sort) {
					$active = TRUE;
				}
			} else {
				$linkParams['sort'] = $this->sort == self::ASC ? self::DESC : self::ASC;

				if ($this->sort == self::ASC) {
					$sort = self::ASC;
				}
				if ($this->sort == self::DESC) {
					$sort = self::DESC;
				}
				$active = TRUE;
			}
		} elseif ($direction) {
			$linkParams['sort'] = $sort = $direction;
		}

		$url = $this->link('sort!', $linkParams);

		$template = $this->template;
		$template->url = $url;
		$template->ajaxRequest = $this->ajaxRequest;
		$template->title = $title ? $title : $column;
		$template->sort = $sort;
		$template->active = $active;
		$template->direction = $direction;

		$template->setFile($this->templateFile);
		$template->render();
	}
}
