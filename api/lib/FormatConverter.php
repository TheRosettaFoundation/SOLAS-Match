<?php

namespace SolasMatch\API\Lib;

class FormatConverter
{
    public static function convertToXliff($doc, $jobid, $filename)
    {
        $url = Common\Lib\Settings::get("converter.supported_formats");
        $url .= "v0/extractor/$filename/Solas-$jobid/";
        $request = new \HTTP_Request2($url, \HTTP_Request2::METHOD_POST);
        $request->setBody($doc);
        $responce = $request->send();
        $doc = $responce->getBody();
        return $doc;
    }

    public static function convertFromXliff($doc, $jobid)
    {
        $url = Common\Lib\Settings::get("converter.supported_formats");
        $url .= "v0/merger/Solas-$jobid";
        $request = new \HTTP_Request2($url, \HTTP_Request2::METHOD_POST);
        $request->setBody($doc);
        $responce = $request->send();
        $doc = $responce->getBody();
        return $doc;
    }
}
