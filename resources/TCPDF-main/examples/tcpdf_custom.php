<?php
// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
    protected $last_page_flag = false;

    public function Header() {
        $hdrhtml ='            
        <table width="100%" cellspacing="0" cellpadding="55%">
        <tr valign="bottom">
              <td class="header1" rowspan="2" align="left" valign="middle"
                    width="34%"><img width="245"  style="text-align:left;" alt="TWB logo"  class="clearlogo" src="/ui/img/cropped-TWB_Logo_horizontal_primary_RGB-1-1.png"></td>
              <td width="35%"></td>
                
              <td class="header1" rowspan="2" align="right" valign="middle"
                    width="19%"><br/><br/><img width="140"  style="text-align:right;" alt="CLEAR Global logo" data-src="/ui/img/CG_Logo_horizontal_primary_RGB.svg" class="clearlogo" src="/ui/img/CG_Logo_horizontal_primary_RGB.svg"></td>
        </tr></table>
        ';        
      $this->writeHTML($hdrhtml, true, false, true, false, ''); 
    }

    public function Close() {
        $this->last_page_flag = true;
        parent::Close();
    }
    
    public function Footer() {
        if ($this->last_page_flag) {
            $tbl = <<<EOD
            <table cellspacing="0" cellpadding="1" border="0">
                <tr>
                    <td rowspan="3"></td>
                    <td></td>
                    <td><img style="border-bottom: 225px solid red;" class="" width="220" src="/ui/img/aimee_sign.png" /></td>
                </tr>
                <tr>
                    <td rowspan="2"></td>
                    <td><hr/></td>
                </tr>
                <tr>
                   <td style="font-size: 8pt;">Aimee Ansari, CEO, CLEAR Global / TWB</td>
                </tr>
                <tr>
                <td colspan="3" align="center"  style="border:1px solid #e8991c;"><span style="">Translators without Borders is part of CLEAR Global, a nonprofit helping people get vital information and be heard, whatever language they speak. We do this through language support, training, data, and technology.</span></td>
               </tr>
               <tr style="font-size: 8pt;">
               <td>
               <span>CLEAR Global/Translators without Borders</span>
               <br/><span>&nbsp;&nbsp;9169 W State St #3055</span>
               <br/><span>&nbsp;&nbsp;Garden City, ID 83714, USA</span>
               </td>
               <td></td>
               <td>
               <span>Email: info@translatorswithoutborders.org</span>
               <span>&nbsp;&nbsp;Website: http://translatorswithoutborders.org</span>
               <span>&nbsp;&nbsp;ref: $this->CustomKey</span>
               </td>
               </tr>
            </table>
            <br/>
            <br/>
            <br/>
            EOD; 
            $this->writeHTML($tbl, true, false, false, false, '');
        } else {
            // ... footer for the normal page ...
            $this->writeHTML('', true, false, false, false, '');
        }

    }
}