<?php

namespace Anyx\SocialUserBundle\Tests\User;

use Anyx\SocialUserBundle\User\AccountFactory;

class AccountFactoryTest extends \PHPUnit_Framework_TestCase
{
	
	public function testResponseFieldsMap()
    {
		
		$userData = array(
			'response'	=> array(
				array(
					'first_name'	=> 'TFN',
					'last_name'		=> 'TLN'
				)
			)
		);
		
		$map = array(
			'testSrv' => array(
				'userName' => 'response.0.first_name + response.0.last_name'
			)
		);
		
		$factory = $this->getFactory( $map );

		$map = '';

		$rf = new \ReflectionMethod($factory, 'findFieldValue');
		$rf->setAccessible(true);
		
		
		$fieldValue = $rf->invokeArgs( $factory, array(
			'field'		=> 'userName',
			'service'	=> 'testSrv',
			'userData'	=> $userData
		));
		
		$this->assertEquals( $fieldValue, 'TFN TLN', 'Account factory is not get complex names' );
	}
	
	/**
	 *
	 * @param array $accountsMap
	 * @param bool $forceCreate
	 * @return \Anyx\SocialUserBundle\User\AccountFactory 
	 */
	private function getFactory( array $accountsMap, $forceCreate = false ) {
		$factory = new AccountFactory( $accountsMap );
		
		return $factory;
	}
}
