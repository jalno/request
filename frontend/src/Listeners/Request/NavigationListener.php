<?php
namespace themes\clipone\Listeners\Request;

use packages\request\Authorization;
use themes\clipone\Navigation;
use themes\clipone\Navigation\MenuItem;

use function packages\userpanel\url;

class NavigationListener {
	public function initial(): void
	{
		if (Authorization::is_accessed('search')) {
			$item = new MenuItem('requests');
			$item->setTitle(t('requests'));
			$item->setURL(url('requests'));
			$item->setIcon('fa fa-exclamation-circle');
			Navigation::addItem($item);
		}
	}
}