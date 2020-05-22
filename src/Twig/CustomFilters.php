<?php

namespace App\Twig;
use App\Service\PoliticumDataAccess;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CustomFilters extends AbstractExtension {

    private $dataAccess;
    private $security;

    /**
     * customFilters constructor.
     * @param PoliticumDataAccess $dataAccess
     * @param Security $security
     */
    public function __construct(PoliticumDataAccess $dataAccess, Security $security){
        $this->dataAccess = $dataAccess;
        $this->security = $security;
    }

    public function getFunctions()
    {
        return [];
    }


    public function getFilters() {
        return [
            new TwigFilter('getUltimoMensajeRecibido', [$this, 'getUltimoMensajeRecibido']), // base64_encode => name of custom filter, base64_en => name of function to execute when this filter is called.
        ];
    }

    public function getUltimoMensajeRecibido(int $input)
    {
        return $this->dataAccess->getUltimoMensajeIntercambiado($input, $this->security->getUser()->getId());
    }

}