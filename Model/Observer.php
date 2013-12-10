<?php  

    class Cammino_CepCorreios_Model_Observer
    {
        public function insertBlock($observer = NULL)
        {
            $_block = $observer->getBlock();
            $_type = $_block->getType();

            //customer/form_register
            //checkout/onepage_billing
            //checkout/onepage_shipping

            if (($_type == "customer/form_register") || ($_type == "checkout/onepage_billing") || ($_type == "checkout/onepage_shipping")) {
                

                // Anotações
                // Ideia é buscar atraves da url de um controller e executar dentro do bloco.
                // exemplo logo abaixo

                $client = new Varien_Http_Client('http://www.example.com/');
                $client->setMethod(Varien_Http_Client::POST);
                $client->setParameterPost('name', $name);
                $client->setParameterPost('address', $address);
                //more parameters
                try{
                    $response = $client->request();
                    if ($response->isSuccessful()) {
                        echo $response->getBody();
                    }
                } catch (Exception $e) {
                }

                // $layout->getUpdate()->addHandle('cammino_cepcorreios_javascript');

                //print_r(get_class($observer->getBlock()->getLayout()));

                // $layout = $observer->getBlock()->getLayout();
                // $update = $layout->getUpdate();

                // $block = $layout->createBlock( 'Cammino_CepCorreios_Block_JavaScript', 'cammino_cepcorreios_javascript', array() );
                // $layout->getBlock('content')->append($block);

                // $layout = $observer->getEvent()->getLayout();

                // var_dump(get_class($update));

                // $layout->renderLayout();

                // $this->loadLayout();
                // $block = $this->getLayout()->createBlock(
                // 'Mage_Core_Block_Template',
                // 'my_block_name_here',
                // array('template' => 'activecodeline/developer.phtml')
                // );
                // $this->getLayout()->getBlock('content')->append($block);
                // //Release layout stream... lol... sounds fancy
                // $this->renderLayout();
            }
        }
    }

?>