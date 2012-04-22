<?php

namespace Anyx\SocialUserBundle\Event;

use Symfony\Component\EventDispatcher\Event as BaseEvent;

/**
 * 
 */
class MergeUsersEvent extends BaseEvent {

	/**
	 * 
	 */
	protected $parentUser;

	/**
	 * 
	 */
	protected $childUser;
	
	/**
	 *
	 * @param mixed $parentUser
	 * @param mixed $childUser 
	 */
	public function __construct( $parentUser, $childUser) {
		$this->parentUser = $parentUser;
		$this->childUser = $childUser;
	}
}