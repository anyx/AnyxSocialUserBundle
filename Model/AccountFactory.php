<?php

namespace Anyx\SocialUserBundle\Model;

/**
 * Description of AccountFactory
 *
 */
class AccountFactory {

	/**
	 * @var string 
	 */
	protected $accountClass = 'Anyx\SocialUserBundle\Model\SocialAccount';

	/**
	 *
	 * @var array
	 */
	protected $accountsMap;

	/**
	 *
	 * @param array $accountsMap 
	 */
	function __construct( array $accountsMap ) {
		$this->accountsMap = $accountsMap;
	}

	/**
	 *
	 * @return Anyx\SocialUserBundle\Model\SocialAccount
	 */
	public function getAccountClass() {
		return $this->accountClass;
	}

	/**
	 *
	 * @param string $accountClass 
	 */
	public function setAccountClass($accountClass) {
		$this->accountClass = $accountClass;
	}
		
	/**
	 *
	 * @param string $service
	 * @param array $userData 
	 */
	public function createAccount( $service, $userData ) {
		$class = $this->getAccountClass();
		$account = new $class;
		
		$account->setAccountId( $this->findFieldValue( 'accountId', $service, $userData ) );
		$account->setServiceName( $service );
		$account->setAccountData( json_encode( $userData ) );
		$account->setUserName( $this->findFieldValue( 'userName', $service, $userData ) );
		
		return $account;
	}
	
	/**
	 * 
	 */
	protected function findFieldValue( $field, $service, $userData ) {

		$map = $this->getAccountMap( $service );
		$fieldPath = $map[$field];		
		
		if( strpos( $fieldPath, '+' ) > 0 ) {
			$parts = explode('+', $fieldPath);
			$values = array();
			foreach ( $parts as $part ) {
				$trimmedPart = trim( $part );
				$values[$trimmedPart] = $this->findScalarFieldValue( $trimmedPart, $service, $userData );
			}
			
			$values['+'] = '';
			$values['  '] = ' ';
			
			return str_replace(array_keys($values), $values, $fieldPath);
			
		} else {
			return $this->findScalarFieldValue( $fieldPath, $service, $userData );
		}
		
	}
	
	protected function findScalarFieldValue( $fieldPath, $service, $userData ) {
		
		foreach( explode( '.', $fieldPath ) as $key ) {
			if (!array_key_exists( $key, $userData ) ) {
				throw new \RuntimeException( "Key '$key' not found in user data" );
			}
			
			$userData = $userData[$key];
		}

		if ( !is_string( $userData ) && !is_numeric( $userData ) ) {
			throw new \RuntimeException( "Value must be scalar" );
		}
		
		return $userData;
	}


	/**
	 *
	 * @param string $service
	 * @return array
	 */
	protected function getAccountMap( $service ) {
		if ( !array_key_exists($service, $this->accountsMap) ) {
			throw new \InvalidArgumentException( "Account fields map not found for service '$service' " );
		} 
		return $this->accountsMap[$service];
	}
}
