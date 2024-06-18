public function getInvoice(Request $request, Response $response, $args)
    {
        [$filename, $file] = $this->get_invoice_pdf($args['invoice_number']);
        $response->getBody()->write($file);
        return $response->withHeader('Content-Type', 'application/pdf')->withHeader('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    public function get_invoice_pdf($invoice_number)
    {
        require_once 'resources/TCPDF-main/examples/tcpdf_include.php';
       
        $userDao = new DAO\UserDao();

        $rows = $userDao->getInvoice($invoice_number);
        if (empty($rows)) return ['none.pdf', 'Not Found'];
        $invoice = $rows[0];

        $TWB = 'TWB-';
        if ($invoice['status']&1) $TWB = 'DRAFT-';
        $invoice_number = $TWB . str_pad($invoice_number, 4, '0', STR_PAD_LEFT);

        $name = $invoice['linguist_name'];

        $status_text = '';
        $badge_style = '';

        $status = $invoice['status'];

        switch ($status) {
        case 0:
            $status_text = 'Invoice';
            $badge_style = "style='font-size: 14px; border: 2px solid green; width: 20px; height: 50px; display: inline-block; padding: 5px; border-radius: 5px; text-align: left;'";
            break;
        case 1:
            $status_text = 'Draft';
            $badge_style = "style='font-size: 14px; border: 2px solid red; width: 20px; height: 50px; display: inline-block; padding: 5px; border-radius: 5px; text-align: left;'";
            break;
        case 2:
            $status_text = 'Invoice Paid';
            $badge_style = "style='font-size: 14px; border: 2px solid green; width: 20px; height: 50px; display: inline-block; padding: 5px; border-radius: 5px; text-align: left;'";
            break;
        case 3:
            $status_text = 'Draft Paid';
            $badge_style = "style='font-size: 14px; border: 2px solid green; width: 20px; height: 50px; display: inline-block; padding: 5px; border-radius: 5px; text-align: left;'";
            break;
        default:
            $status_text = 'Draft Paid';
            $badge_style = "style='font-size: 14px; border: 2px solid green; width: 20px; height: 50px; display: inline-block; padding: 5px; border-radius: 5px; text-align: left;'";
        }

        // Use $status_text and $badge_style in your HTML

      
        $email = $invoice['email'];
        $country = $invoice['country'];
        $date = date("Y-m-d" , strtotime($invoice['invoice_date']));
        $amount = '$' . round($invoice['amount'], 2);


         // column titles
        $header = array('S/N', 'Description', 'PO', 'Quantity', 'Unit Price','Amount');

        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('TWB Platform');
        $pdf->SetTitle("Invoice");
        $pdf->SetSubject('Generate Linguist Invoice');
        $pdf->SetKeywords('TWB Platform,Linguist Invoice');
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, [0, 64, 255], [0, 64, 128]);
        $pdf->setFooterData([0, 64, 0], [0, 64, 128]);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('dejavusans', '', 9, '', false);
        $pdf->AddPage('L');
        $pdf->SetLineStyle(['width' => 5, 'color' => [232, 153, 28]]);
        $pdf->Line(0, 0, $pdf->getPageWidth(), 0);
        $pdf->Line($pdf->getPageWidth(), 0, $pdf->getPageWidth(), $pdf->getPageHeight());
        $pdf->Line(0, $pdf->getPageHeight(), $pdf->getPageWidth(), $pdf->getPageHeight());
        $pdf->Line(0, 0, 0, $pdf->getPageHeight());

        
$html = <<<EOF
        <style>
 
        div.test {
            color: #000000;
            font-size: 13pt;
            border-style: solid solid solid solid;
            border-width: 8px 8px 8px 8px;
            border-color: #FFFFFF;
            text-align: center;
            margin: 50px auto;
        }
        .uppercase {
            text-transform: uppercase;
            font-weight:bold;
        }
        .footer {
            text-align: center;
            font-size: 11pt;
        }
        .footer-main {
            text-align:center;
        }
        </style>
    
         <table width="100%" cellspacing="0" cellpadding="55%">
        <tr valign="bottom">
              <td class="header1" rowspan="2" align="left" valign="middle"
                    width="33%"><br/>
                      <img width="140"  style="margin-bottom:14px;" alt="CLEAR Global logo" data-src="/ui/img/CG_Logo_horizontal_primary_RGB.svg" class="clearlogo" src="/ui/img/CG_Logo_horizontal_primary_RGB.svg">
                  
                    </td>
              <td width="35%"></td>  
              <td class="header1" rowspan="2" align="left" valign="middle"
                    width="25%">
                    <div style="font-weight:bold; float:left ; font-size:24px; text-transform:uppercase">$status_text</div>
            
                    </td>
        </tr>
        
        </table>

EOF;

$badge = <<<EOF
       
         <table width="100%" cellspacing="0" cellpadding="55%">
        <tr valign="bottom">
              <td class="header1" rowspan="2" align="left" valign="middle"
                    width="33%"><br/>
                      
                  
                    </td>
              <td width="35%"></td>  
              <td class="header1" rowspan="2" align="left" valign="middle"
                    width="8%">
                        <div style='font-size: 14px; border: 2px solid black; width: 20px; height: 50px; display: inline-block; padding: 5px; border-radius: 5px; text-align: left;'>
                                $status_text
                            </div>
     
                    <br/><br/>
                    </td>
        </tr>
        
        </table>
        <br/>
        <br/>


EOF;
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->writeHTML($badge, true, false, true, false, '');

    $html1 = <<<EOF
<table width="100%" cellspacing="0" cellpadding="5%">  <tr valign="bottom">
        <td class="header1" rowspan="2" align="left" valign="middle" width="33%">
            <br/>
            <div style="font-weight:bold;">From</div>
            <div>$name</div>
            <div>$email</div>
            <div>$country</div>
        </td>
        <td width="34%">  </td>
        <td class="header1" rowspan="2" align="left" valign="middle" width="33%">  <div>Invoice No: $invoice_number</div>  <div>$date</div>
            <br/><br/>
        </td>
    </tr>
</table>
<div style="margin-top:20px;">
    <br/>
    <br/>
    <div style="font-weight:bold;">To</div>
    <div>CLEAR Global Inc.</div>  <div>9169 W State St #83714</div>  <div>+ 1 (203) 794-6698</div>  </div>
<br/>
EOF;

// ... rest of your TCPDF code

    $pdf->writeHTML($html1, true, false, true, false, '');
        $tbl = <<<EOF
        <table border="1" cellpadding="2" cellspacing="2">
        <thead>
        <tr style="background-color:#FAFAFA;color:black;">
        <td width="30" align="center"><b>S/N</b></td>
        <td width="300" style="padding-right:10px;"><b>Description</b></td>
        <td width="140" align="center"><b>PO</b></td>
        <td width="200" align="center"> <b>Quantity</b></td>
        <td width="100" align="center"><b>Unit Price</b></td>
        <td width="100" align="center"><b>Amount</b></td>
        </tr>
        </thead>
        EOF;

$total = 0 ;

foreach ($rows as $index => $row) {
    $purchase_order = $row['purchase_order'];
    $description = $row['title'];
    $type = $row['type_text'];
    $language = $row['language_pair_name'];
    $project = $row['project_title'];
    $row_amount = '$' . round($row['row_amount'], 2);
    $unit = $row['pricing_and_recognition_unit_text_hours'];
    $unit_rate = '$' . $row['unit_rate'];
    $quantity = round($row['quantity'], 2);
    $total += round($row['row_amount'], 2) ;
    $number = $index + 1;


    $tbl .= <<<EOF
    <tr>
    <td width="30" align="center"><b>$number</b></td>
    <td width="300"  style="padding-right:10px; padding-top:10px;"> $description <br /><span style="font-weight:bold;"> $project </span> <br />$language <br />$type<br /></td>
    <td width="140" align="center">$purchase_order</td>
    <td width="200" align="center">$unit</td>
    <td width="100" align="center">$unit_rate</td>
    <td align="center" width="100">$row_amount</td>
    </tr>
    EOF;
}

    $tbl .= <<<EOF
    <tr>
    <td colspan="5" style="font-weight:bold;">Total</td>
    <td width="100" align="center"> $ $total</td>
    </tr>
    </table>
    EOF;


    $pdf->writeHTML($tbl, true, false, false, false, '');
    $pdf->lastPage();

    return [$invoice['filename'], $pdf->Output($invoice['filename'], 'S')];
    }