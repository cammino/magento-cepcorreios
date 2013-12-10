<?php
class Cammino_CepCorreios_SearchController extends Mage_Core_Controller_Front_Action {
	
	private $_cep;

	public function indexAction() {
		// $this->getAddress();

		return '<script>alert("oi");</script>';
	}
	

	private function getAddress()
	{
		
		$this->_cep = $_GET['cep'];

		if(!$this->_cep){
			return json_encode(array('erro' => 'Cep Inválido'));
		}else{	

			$html =  $this->accessCorreios($this->_cep);                           
			  
			preg_match_all('#<span class="respostadestaque">(.+?)</span>#si', $html, $result, PREG_SET_ORDER );

			$city_state = explode("/", str_replace( "\n", "", $result[2][1] ) );
			$street = explode('-', $result[0][1])[0];
			
			if(count($result) > 0){
				$endereco = array( 
					'logradouro' => utf8_encode(trim($street)), 
					'bairro' => utf8_encode(trim($result[1][1])), 
					'cidade' => utf8_encode(trim($city_state[0])), 
					'uf' => $city_state[1] 
				);
			}
			else{ 
				$endereco = array('erro' => 'Endereço não encontrado');
			}

			return json_encode($endereco);
		}
	}

	private function accessCorreios()
	{

		$get  = array();
		$post = array( 'cepEntrada' => $this->_cep, 'tipoCep' => '', 'cepTemp' => '', 'metodo' => 'buscarCep' );
		$url  = explode('?','http://m.correios.com.br/movel/buscaCepConfirma.do',2);

		if(count($url)===2){
			$temp_get = array();
			parse_str($url[1],$temp_get);
			$get = array_merge($get,$temp_get);
		}

		$ch = curl_init($url[0]."?".http_build_query($get));
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, http_build_query($post));
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		return curl_exec ($ch);
	}

}