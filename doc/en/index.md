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

```yml
extensions:
	nasext.sortingControl: NasExt\Controls\DI\SortingControlExtension
```

## Usage
Inject NasExt\Controls\ISortingControlFactory in to presenter

```php
class FooPresenter extends Presenter
{

	/** @var  NasExt\Controls\ISortingControlFactory */
	private $sortingControlFactory;

	/**
	 * INJECT SortingControlFactory
	 * @param NasExt\Controls\ISortingControlFactory $sortingControlFactory
	 */
	public function injectItemsPerPageFactory(NasExt\Controls\ISortingControlFactory $sortingControlFactory)
	{
		$this->sortingControlFactory = $sortingControlFactory;
	}

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

		$control = $this->sortingControlFactory->create($columns, 'name', SortingControl::ASC);

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

		$control = $this->sortingControlFactory->create($columns, 'name', SortingControl::ASC);

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

###Setting to SortingControl remember last sorting on the page
For remember last sorting use setSaveSorting()
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

		$control = $this->sortingControlFactory->create($columns, 'name', SortingControl::ASC);

		// enable remember last sorting
		$control->setSaveSorting();

		return $control;
	}
```

###Set templateFile for SortingControl
For set templateFile use templateFile param
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

		$control = $this->sortingControlFactory->create($columns, 'name', SortingControl::ASC);
		$control->templateFile = 'myTemplate.latte';

		return $control;
	}
```

-----

Repository [http://github.com/nasext/sortingcontrol](http://github.com/nasext/sortingcontrol).