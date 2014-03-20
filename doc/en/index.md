NasExt/SortingControl
===========================

Sorting control for Nette Framework.

Requirements
------------

NasExt/SortingControl requires PHP 5.3.2 or higher.

- [Nette Framework](https://github.com/nette/nette)

Installation
------------

The best way to install NasExt/SortingControl is using  [Composer](http://getcomposer.org/):

```sh
$ composer require nasext/sorting-control
```


## Usage

```php
class FooPresenter extends Presenter
{

	public function renderDefault()
	{
		/** @var NasExt\Controls\SortingControl $sorting */
		$sorting = $this->getComponent('sorting');
		$sorting->getSort(); // return array, use for sorting
		// or $sorting->getColumn(), $sorting->getSortDirection()
	}


	/**
	 * @return NasExt\Controls\SortingControl
	 */
	protected function createComponentSorting()
	{
		$columns = array(
			'name' => array('u.name', 'u.surname'),
			'email' => 'u.email',
			'status' => 'u.status'
		);

		$control = new NasExt\Controls\SortingControl($columns, 'name', SortingControl::ASC);

		return $control;
	}
}
```

###Use control in layout
```php
	// first parameter is name of defined column in control
	// second parameter is title for display
	{control sorting, "name", "Name title"}
```


###SortingControl with ajax
For use SortingControl with ajax use setAjaxRequest() and use events onShort[] for invalidateControl
```php
	/**
	 * @return NasExt\Controls\SortingControl
	 */
	protected function createComponentSorting($name)
	{
		$columns = array(
			'name' => array('u.name', 'u.surname'),
			'email' => 'u.email',
			'status' => 'u.status'
		);

		$control = new NasExt\Controls\SortingControl($columns, 'name', SortingControl::ASC);

		// enable ajax request, default is false
		$control->setAjaxRequest();

		$that = $this;
		$control->onShort[] = function ($control) use ($that) {
			if ($that->isAjax()) {
				$that->redrawControl();
			}
		};

		return $control;
	}
```

###Set templateFile for SortingControl
For set templateFile use setTemplateFile()
```php
	/**
	 * @return NasExt\Controls\SortingControl
	 */
	protected function createComponentSorting($name)
	{
		$columns = array(
			'name' => array('u.name', 'u.surname'),
			'email' => 'u.email',
			'status' => 'u.status'
		);

		$control = new NasExt\Controls\SortingControl($columns, 'name', SortingControl::ASC);
		$control->setTemplateFile('myTemplate.latte');

		return $control;
	}
```

-----

Repository [http://github.com/nasext/sortingcontrol](http://github.com/nasext/sortingcontrol).