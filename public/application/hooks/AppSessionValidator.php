<?php


class AppSessionValidator
{

    private $ci;
    private $strRedirectUrl = "/auth/?r=";
    private $currentController;
    private $arrExludedControllers = array("auth");



    public function __construct()
    {
        $this->ci = &get_instance();
        $this->currentController = $this->ci->router->class;

        $url = $this->ci->config->site_url($this->ci->uri->uri_string());
        $url = $_SERVER['QUERY_STRING'] ? $url . '?' . $_SERVER['QUERY_STRING'] : $url;
        $base64_url = base64_encode($url);
        $this->strRedirectUrl = $this->strRedirectUrl . $base64_url;
    }

    public function initialize()
    {
        if (!$this->ci->session->userdata("email") && !in_array($this->currentController, $this->arrExludedControllers)) {
            redirect($this->strRedirectUrl);
        }
    }
}
