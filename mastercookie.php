<?php
# /modules/gtmmastercookie/gtmmastercookie.php

/**
 * Define a master cookie to allow cookie restore with AddingWell - A Prestashop Module
 * 
 * 
 * 
 * @author Benjamin Bendaoui <benjamin@aerographediscount.fr>
 * @version 0.0.1
 */

 if (!defined('_PS_VERSION_')) exit;

 class MasterCookie extends Module
 {
     public function __construct()
     {
         $this->name = 'mastercookie';
         $this->tab = 'administration';
         $this->version = '1.0.0';
         $this->author = 'Bendaoui Benjamin';
         $this->need_instance = 0;
         $this->bootstrap = false;
 
         parent::__construct();
 
         $this->displayName = $this->l('Master Cookie');
         $this->description = $this->l('Définit un Master Cookie pour le site.');
 
         $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module?');
     }
 
     public function install()
     {
         return parent::install() && $this->registerHook('displayHeader');
     }
 
     public function uninstall()
     {
         return parent::uninstall() && $this->unregisterHook('displayHeader');
     }
 
     public function hookDisplayHeader()
     {
         $this->setMasterCookie();
     }
 
     private function setMasterCookie()
     {
         $cookie_domain = $this->getCookieDomain(); // Automatically get the domain
         $expiry_time = time() + (365 * 24 * 60 * 60);
 
         if (!isset($_COOKIE['master_cookie'])) {
             $uid = uniqid('', true);
             setcookie('master_cookie', $uid, $expiry_time, '/', $cookie_domain, true, true);
             $_COOKIE['master_cookie'] = $uid;
         } else {
             // Update the expiration date of the existing cookie
             $uid = $_COOKIE['master_cookie'];
             setcookie('master_cookie', $uid, $expiry_time, '/', $cookie_domain, true, true);
         }
     }
 
     private function getCookieDomain()
     {
         $host = $_SERVER['HTTP_HOST'];
         $domain_parts = explode('.', $host);
         $domain = $host;
 
         // Handle second-level domains like .co.uk, .com.au, etc.
         $second_level_domains = [
             'co.uk', 'com.au', 'co.in', 'co.nz', 'co.jp', 'co.kr', 'co.za', 'com.br'
         ];
 
         $last_two_parts = implode('.', array_slice($domain_parts, -2, 2));
 
         if (in_array($last_two_parts, $second_level_domains) && count($domain_parts) > 2) {
             $domain = implode('.', array_slice($domain_parts, -3, 3));
         } elseif (count($domain_parts) > 2) {
             $domain = implode('.', array_slice($domain_parts, -2, 2));
         }
 
         return '.' . $domain; // Add the dot to include subdomains
     }
 }