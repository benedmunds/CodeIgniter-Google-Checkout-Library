<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Name:  Google Checkout
 * 
 * Location: http://github.com/benedmunds/CodeIgniter-Google-Checkout-Library  
 * 
 * Author:  Ben Edmunds
 *          ben.edmunds@gmail.com
 *          @benedmunds 
 * 
 * Created:  10.22.2009 
 * 
 * Description:  Library to interface with Google Checkout API.  This is basically a wrapper for Google's code.
 * 
 */


class gcheckout 
{
	/**
	 * CodeIgniter global
	 *
	 **/
	protected $ci;
	
	/**
	 * Checkout Variables/Objects/Arrays/Stuff
	 */
	protected $cart;
	protected $items;
	protected $merchant_id;
	protected $mercant_key;
	protected $env;
	

	/**
	 * __construct
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function __construct()
	{
		//Get an instance of the CodeIgniter super-duper object
		$this->ci =& get_instance();
		
		//Include all the required files
		require_once('googlecart.php');
		require_once('googleitem.php');
		require_once('googleshipping.php');
		require_once('googletax.php');
	}
	
	/**
	 * init
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function init($merchant_id, $merchant_key, $env='sandbox', $currency='USD')
	{
	   $this->merchant_id  = $merchant_id;
	   $this->merchant_key = $merchant_key;	
	   $this->env          = $env;
	   $this->currency     = $currency;
		
	   //create the cart
	   $this->cart = new GoogleCart($merchant_id, $merchant_key, $env, $currency);
	}
	
	/**
	 * add item
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function add_item($name, $description, $quantity, $price, $url=false, $email=false)
	{
	   //get the next key in the items array
	   $key = count($this->items);
	   
	   //create a new item
	   $this->items[$key] = new GoogleItem($name, $description, $quantity, $price);
	   
	   //set the URL for digital content delivery if set
	   if ($url) {
  	      $this->items[$key]->SetURLDigitalContent($url, $description, $name);
	   }
	   
	   //set for email digital delivery if set
	   if ($email) {
	      $this->items[$key]->SetEmailDigitalDelivery('true');
	   }

	   //finally add the item to the cart
	   $this->cart->AddItem($this->items[$key]);
	}
	
	
	/**
	 * set cart urls
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_cart_urls($edit_url, $shop_url)
	{
	   $this->cart->SetEditCartUrl($edit_url);
	   $this->cart->SetContinueShoppingUrl($shop_url);
	}
	
	/**
	 * get form action
	 * 
	 * @return action
	 * @author Ben Edmunds
	 **/
	public function get_form_action()
	{
		 return "https://" . $this->env . ".google.com/cws/v2/Merchant/" . $this->merchant_id . "/checkout";
	}
	
	/**
	 * get form button
	 * 
	 * @return src
	 * @author Ben Edmunds
	 **/
	public function get_form_button($cart_url)
	{
		 return $this->cart->CheckoutServer2ServerButton($cart_url);
	}
	
	/**
	 * get signature
	 * 
	 * @return signature
	 * @author Ben Edmunds
	 **/
	public function get_signature()
	{
		 return base64_encode($this->cart->getSignature($this->cart->getCart()));
	}
	
	/**
	 * do checkout
	 *
	 * @return cart object
	 * @author Ben Edmunds
	 **/
	public function do_checkout()
	{
	   // This will do a server-2-server cart post and send an HTTP 302 redirect status
	   // This is the best way to do it if implementing digital delivery
	   // More info http://code.google.com/apis/checkout/developer/index.html#alternate_technique
  	   list($status, $error) = $this->cart->CheckoutServer2Server();
  	   
       // if we reach this point, something went terribly, terribly wrong
       return $status .','. $error;
	}
	
	/**
	 * get cart
	 *
	 * @return cart
	 * @author Ben Edmunds
	 **/
	public function get_cart()
	{
	   return base64_encode($this->cart->GetXML());
	}
	
	/**
	 * get xml
	 *
	 * @return xml
	 * @author Ben Edmunds
	 **/
	public function get_xml()
	{
	   return $this->cart->GetXML();
	}

	/**
	 * output xml for debug
	 *
	 * @return xml output
	 * @author Ben Edmunds
	 **/
	public function output_xml()
	{
	   header('Content-type: text/xml');
	   echo $this->cart->GetXML();
	}
	
	
	
}
