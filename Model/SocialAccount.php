<?php

namespace Anyx\SocialUserBundle\Model;

use Anyx\SocialUserBundle\Util\ValueFinder;
/**
 * 
 */
class SocialAccount
{
    
    protected static $dataMap;

    /**
	 * 
     */
    protected $id;
	
	/**
	 * 
	 */
	protected $serviceName;
	
	/**
	 * 
	 */
	protected $accountId;

	/**
	 * @var string
	 */
	protected $accountData;

    /**
     * @var array
     */
    protected $data;

    /**
	 *
	 */
	public function getId()
    {
		return $this->id;
	}

	/**
	 *
	 */
	public function setId($id)
    {
		$this->id = $id;
	}

	/**
	 *
	 */
	public function getServiceName()
    {
		return $this->serviceName;
	}

	/**
	 *
	 */
	public function setServiceName($serviceName)
    {
		$this->serviceName = $serviceName;
	}

	/**
	 *
	 */
	public function getAccountId()
    {
		return $this->accountId;
	}

	/**
	 *
	 */
	public function getAccountData()
    {
		return $this->accountData;
	}

    /**
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     *
     * @param array $data 
     */
    public function setData($data)
    {
        $this->data = $data;
        $this->setAccountId( $this->getValue('accountId') );
    }

    /**
     *
     * @param array $map 
     */
    public static function setCommonDataMap( array $map )
    {
        self::$dataMap = $map;
    }
    
    /**
     *
     * @return array
     */
    public function getDataMap()
    {
        return self::$dataMap[$this->getServiceName()];
    } 

    /**
     * 
     */
    public function serializeData() {
        $this->accountData = json_encode($this->data);
    }

    /**
     * 
     */
    public function deserializeData() {
        $this->data = json_decode($this->accountData);
    }

    /**
     *
     * @param string $field
     * @return string
     */
    public function getValue( $field ) {
        return ValueFinder::findFieldValue( $field, $this->getData(), $this->getDataMap() );
    }

	/**
	 *
	 */
	protected function setAccountId($accountId)
    {
		$this->accountId = $accountId;
	}
}