<?php

namespace PhoneArena;

// @author: juanpablocs21@gmail.com
// @class: PhoneArena\Scraper
// @description: scraping all
class Scraper extends HttpRequest
{
    var $urlCompanys = "/phones/manufacturers";
    var $urlCompany = "/phones/manufacturers/%s/page/%d";
    
    var $companyUrl;
    var $companyImage;
    var $companyName;
    var $companyScraping;

    var $phoneUrl;
    var $phoneImage;
    var $phoneName;
    var $phoneScraping;

    function getCompany()
    {
        $html = $this->getRequest($this->urlCompanys);
        preg_match_all('#<div class="s_block_4 s_block_4_s115 clearfix ">(.*?)</div>#si', $html, $r_g);
        // var_dump($r_g);
        if(empty($r_g[1]))
            throw new PhoneArenaException('error scraping manufacturers');

        $m = array();
        $total = count($r_g[1]);
        for ($i=0; $i < $total; $i++) 
        {
            $this->putCompany($r_g[1][$i]);

            $m[] = array(
                    'url'		=> 	$this->getCompanyUrl(), 
                    'image'		=>	$this->getCompanyImage(),
                    'company' 	=>	$this->getCompanyName(),
                    'scraping' 	=> 	$this->getCompanyScraping()
                );
        }
        return array('error'=>false, 'message'=>'fetch correct', 'data'=>$m);
    }
    
    function getPhonesByCompany($slug, $page=1)
    {
        $html = $this->getRequest(sprintf($this->urlCompany, $slug, $page));
        preg_match_all('#<div class="s_block_4 s_block_4_s115 (?:.*?) clearfix" data-sub_obj_type=\'(?:.*?)\' data-obj_id=\'(?:.*?)\' >(.*?)<div class=\'toolTip\' style=\'display: none;\'>#si', $html, $itms);
        if(empty($itms[1]))
            throw new PhoneArenaException('error scraping phones by companys');

        $r = array();
        $total = count($itms[1]);
        for ($i=0; $i < $total; $i++) 
        {
            $this->putPhonesByCompany($itms[1][$i]);
            $r[] = array(
                    'url'		=> 	$this->getPhoneUrl(), 
                    'image'		=>	$this->getPhoneImage(),
                    'phone' 	=>	$this->getPhoneName(),
                    'scraping' 	=> 	$this->getPhoneScraping()
                );
        }
        return array('error'=>false, 'message'=>'fetch correct', 'total'=>$total, 'page'=>$page, 'data'=>$r);

    }

    function putPhonesByCompany($buffer)
    {
        preg_match('#<a class="s_thumb" href="([^"]+)" >#si', $buffer, $lnk);
        preg_match('#<img src="([^"]+)" alt="(?:[^"]+)" widht="(?:[^"]+)" height="(?:[^"]+)" />#si', $buffer, $img);
        preg_match('#>([^<]+)</a></h3>#si', $buffer, $name);

        $this->phoneUrl = @$lnk[1];
        $this->phoneImage = "http:".@$img[1];
        $this->phoneName = @$name[1];
        $this->phoneScraping = @$lnk[1];
    }

    function putCompany($buffer)
    {
        preg_match('#<a href="([^"]+)" class="s_thumb">#si', $buffer, $lnk);
        preg_match('#<img src="([^"]+)" />#si', $buffer, $img);
        preg_match('#<h3><a href="">([^<]+)</a></h3>#si', $buffer, $name);

        $this->companyUrl = @$lnk[1];
        $this->companyImage = @$img[1];
        $this->companyName = @$name[1];
        $this->companyScraping = str_replace(' ','-',@$name[1]);
    }
    function getCompanyUrl()
    {
        return $this->urlbase.$this->companyUrl;
    }	
    function getCompanyImage()
    {
        return $this->companyImage;
    }
    function getCompanyName()
    {
        return $this->companyName;
    }
    function getCompanyScraping()
    {
        return $this->companyScraping;
    }
    function getPhoneUrl()
    {
        return $this->urlbase.$this->phoneUrl;
    }	
    function getPhoneImage()
    {
        return $this->phoneImage;
    }
    function getPhoneName()
    {
        return $this->phoneName;
    }
    function getPhoneScraping()
    {
        return $this->phoneScraping;
    }
}