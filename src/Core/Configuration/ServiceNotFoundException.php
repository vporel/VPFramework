<?php
namespace VPFramework\Core\Configuration;

class ServiceNotFoundException extends \Exception
{
    private $serviceName;
    public function __construct($serviceName)
    {
        $this->serviceName = $serviceName;
        parent::__construct("Le service $serviceName n'est pas défini");
    }

    public function getServiceName(){ return $this->serviceName; }
}