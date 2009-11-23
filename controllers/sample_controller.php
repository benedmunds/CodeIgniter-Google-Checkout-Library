<?php
class Cart extends Controller {

    function Cart() {
        parent::Controller();
    }
    
    function checkout() {
        //load the google checkout library
        $this->load->library('google_checkout/gcheckout');
        
        //setup the variables for google checkout (of course you don't have to do iot this way but this is pulling values from a config file)
        $merchant_id  = $this->config->item('checkout_merchant_id');
        $merchant_key = $this->config->item('checkout_merchant_key');
        $server_type  = $this->config->item('checkout_server_type');
        $currency     = $this->config->item('checkout_currency');
        $edit_url     = $this->config->item('checkout_edit_url');
        $shop_url     = $this->config->item('checkout_shop_url');
        
        //initialize the checkout
        $this->gcheckout->init($merchant_id, $merchant_key, $server_type);
        
        //set the trackback urls
        $this->gcheckout->set_cart_urls($edit_url, $shop_url);
        
        //sample items
        $items   = Array();
        $items[] = Array('title'       => 'Item 1',
                         'description' => 'This is a test',
                         'price'       => 19.95,
                        );
        $items[] = Array('title'       => 'Item 2',
                         'description' => 'This is another test',
                         'price'       => 29.99,
                        );
                        
        //add the items to the google cart
        foreach ($items as $item) {
        	$this->gcheckout->add_item($item['title'], $item['description'], 1, $item['price']);
        }
        
        
        //$this->gcheckout->do_checkout();
        $this->gcheckout->output_xml();
    }
}	
?>