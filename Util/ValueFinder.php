<?php

namespace Anyx\SocialUserBundle\Util;


class ValueFinder
{
    /**
     *
     * @param string $field
     * @param array $data
     * @param array $map
     * @return mixed 
     */
	public static function findFieldValue( $field, array $data, array $map )
    {
        if ( !array_key_exists($field, $map) ) {
            return null;
        }
        
		$fieldPath = $map[$field];

        if( strpos( $fieldPath, '+' ) > 0 ) {
			$parts = explode('+', $fieldPath);
			$values = array();
			foreach ( $parts as $part ) {
				$trimmedPart = trim( $part );
				$values[$trimmedPart] = self::findScalarFieldValue( $trimmedPart, $data );
			}
			
			$values['+'] = '';
			$values['  '] = ' ';
			
			return str_replace(array_keys($values), $values, $fieldPath);
		} else {
			return self::findScalarFieldValue( $fieldPath, $data );
		}
	}
	
    /**
     *
     * @param string $fieldPath
     * @param array $data
     * @return mixed
     * @throws \RuntimeException 
     */
	protected static function findScalarFieldValue( $fieldPath, $data )
    {
		foreach( explode( '.', $fieldPath ) as $key ) {
			if (!array_key_exists( $key, $data ) ) {
				throw new \RuntimeException( "Key '$key' not found in user data" );
			}
			
			$data = $data[$key];
		}

		if ( !is_string( $data ) && !is_numeric( $data ) ) {
			throw new \RuntimeException( "Value must be scalar" );
		}
		
		return $data;
	}
}
