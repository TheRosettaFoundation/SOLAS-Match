<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FormatConverter
 *
 * @author sean
 */
class FormatConverter {
    public static function convertToXliff($doc,$jobid, $filename){
        $url = Settings::get("converter.supported_formats");
        $url.= "v0/extractor/{$filename}/Solas-{$jobid}/";
        $request = new HttpRequest($url);
        $request->setBody($doc);
        $request->setMethod(HTTP_METH_POST);
        $responce = $request->send();
        $doc=$responce->getBody();
        return $doc;
    }
    public static function convertFromXliff($doc,$jobid){
        $url = Settings::get("converter.supported_formats");
        $url.= "v0/merger/Solas-{$jobid}";
        $request = new HttpRequest($url);
        $request->setBody($doc);
        $request->setMethod(HTTP_METH_POST);
        $responce = $request->send();
        $doc=$responce->getBody();
        return $doc;
        return $doc;
    }
}

?>
