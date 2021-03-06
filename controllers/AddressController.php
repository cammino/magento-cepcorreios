<?php
class Cammino_Cepcorreios_AddressController extends Mage_Core_Controller_Front_Action {
	
	private $_postcode;
	private $_jsonData;

	public function searchAction() {
		$this->_postcode = $this->getRequest()->getParam('postcode');
		$this->getAddress();
		
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody($this->_jsonData);
	}

	private function getAddress()
	{
		if (!$this->_postcode) {
			$this->_jsonData = json_encode(array('erro' => 'Cep Inválido'));
		} else {

			$this->_postcode = str_replace('-', '', $this->_postcode);
			$html = $this->accessCorreiosAction(true);

			if(count($html) > 0){
				$address = array (
					'city' 	    => $html->cidade,
					'street1'   => $html->end,
					'street3'   => $html->bairro,
					'region'    => $html->uf,
                    'region_id' => $this->getRegionId($html->uf)
				);
				$this->_jsonData = json_encode($address);

			}
			else{ 
				$this->_jsonData = json_encode(array('erro' => 'Endereço não encontrado'));
			}
		}
	}

	public function accessCorreiosAction($simpleReturn = false)
	{
		if ($this->getRequest()->getPost()) {
            $cep = $this->getRequest()->getPost('cep', false);
        } else if ($this->getRequest()->getQuery('cep', false)) {
            $cep = $this->getRequest()->getQuery('cep', false);
        }
        else {
        	$cep = $this->getRequest()->getParam('postcode');
        }
        $cep = preg_replace('/[^\d]/', '', $cep);
        Mage::getSingleton('core/session')->setCep($cep);
        $soapArgs = array(
            'cep' => $cep,
            'encoding' => 'UTF-8',
            'exceptions' => 0
        );
        $return = '';

        try {
            $clientSoap = new SoapClient("https://apps.correios.com.br/SigepMasterJPA/AtendeClienteService/AtendeCliente?wsdl", array(
                'soap_version' => SOAP_1_1, 'encoding' => 'utf-8', 'trace' => true, 'exceptions' => true,
                'cache_wsdl' => WSDL_CACHE_BOTH, 'connection_timeout' => 5
            ));
            $result = $clientSoap->consultaCep($soapArgs);
            $dados = $result->return;
            if ($simpleReturn)
        		return $dados;

            if (is_soap_fault($result)) {
                $return = "{ 'uf' : '', 'cidade' : '', 'bairro' : '', 'tipo_logradouro' : '', 'logradouro' : '', 'resultado' : '0', 'resultado_txt' : 'cep nao encontrado' }";
            }else{
                $return = "{ 'uf' : '".$dados->uf."', 'cidade' : '".$dados->cidade."', 'bairro' : '".$dados->bairro."', 'tipo_logradouro' : '', 'logradouro' : '".$dados->end."', 'resultado' : '1', 'resultado_txt' : 'sucesso%20-%20cep%20completo' }";
            }

        } catch (SoapFault $e) {
            $return = "{ 'uf' : '', 'cidade' : '', 'bairro' : '', 'tipo_logradouro' : '', 'logradouro' : '', 'resultado' : '0', 'resultado_txt' : 'cep nao encontrado' }";
        } catch (Exception $e) {
            $return = "{ 'uf' : '', 'cidade' : '', 'bairro' : '', 'tipo_logradouro' : '', 'logradouro' : '', 'resultado' : '0', 'resultado_txt' : 'cep nao encontrado' }";
        }
        
        echo ($return);die;
    }

    // retorna código do estado, dado a UF
    private function getRegionId($region) {
        $collection = Mage::getResourceModel('directory/region_collection')->addCountryFilter("BR")->addRegionCodeFilter($region)->load()->getItems();
        if (!empty($collection)) {
            return end($collection)->getRegionId();
        } else {
            return $region;
        }
    }  

}