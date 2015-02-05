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
			$html = $this->accessCorreios();

			preg_match_all('#<span class="respostadestaque">(.+?)</span>#si', $html, $result, PREG_SET_ORDER );
			
			if(count($result) > 0){


				if (count($result) > 2) {
					$city_region = explode('/', str_replace( "\n", '', $result[2][1]));
					
					$address = array(
						'street1' => utf8_encode(trim(current(explode(' - ', $result[0][1])))),
						'street3' => utf8_encode(trim($result[1][1])),
						'city' 	  => utf8_encode(trim($city_region[0])),
						'region'  => $this->getRegionId((trim($city_region[1])))
					);
				} else {
					$city_region = explode('/', str_replace( "\n", '', $result[0][1]));
					
					$address = array (
						'city' 	  => utf8_encode(trim($city_region[0])),
						'region'  => $this->getRegionId((trim($city_region[1])))
					);
				}

				$this->_jsonData = json_encode($address);

			}
			else{ 
				$this->_jsonData = json_encode(array('erro' => 'Endereço não encontrado'));
			}
		}
	}

	private function accessCorreios()
	{
		$get  = array();
		$post = array( 'cepEntrada' => $this->_postcode, 'tipoCep' => '', 'cepTemp' => '', 'metodo' => 'buscarCep' );
		$url  = explode('?','http://m.correios.com.br/movel/buscaCepConfirma.do',2);

		if(count($url)===2){
			$temp_get = array();
			parse_str($url[1],$temp_get);
			$get = array_merge($get,$temp_get);
		}

		$ch = curl_init($url[0].'?'.http_build_query($get));
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, http_build_query($post));
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		return curl_exec ($ch);
	}

	private function getRegionId($region)
	{
		$collection = Mage::getResourceModel('directory/region_collection')->addCountryFilter("BR")->addRegionCodeFilter($region)->load()->getItems();
		if (!empty($collection)) {
			return end($collection)->getRegionId();
		} else {
			return "";
		}
	}

}