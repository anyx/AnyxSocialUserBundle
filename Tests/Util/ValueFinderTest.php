<?php

namespace Anyx\SocialUserBundle\Tests\User;

use Anyx\SocialUserBundle\Util\ValueFinder;

class ValueFinderTest extends \PHPUnit_Framework_TestCase
{
	
	public function testComplexFieldsMap()
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
            'userName' => 'response.0.first_name + response.0.last_name'
		);
		
		$fieldValue = ValueFinder::findFieldValue ('userName', $userData, $map);

		$this->assertEquals( $fieldValue, 'TFN TLN', 'ValueFinder is not get complex values' );
	}
}
