<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" >
<head>
    <!-- Editor Hint: ¿áéíóú -->
    <meta charset="utf-8" content="application/xhtml+xml" />
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/bootstrap/css/bootstrap.min1.css"/>
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/css/style.1.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/jquery-ui.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/solas3.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css"/>
    <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-1.9.0.js"></script>
    <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-ui.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function(){
        var table = $('#myTable').DataTable({ "paging": false });
        table.order([0, 'asc'], [ 1, 'asc' ]).draw();
      });
    </script>

    <style>
        .container {
            width: 94%;
            margin-left: 3%;
            margin-right: 3%;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;">All Deals</h2>

    <table id="myTable" style="overflow-wrap: break-word;" class="container table table-striped">
        <thead>
            <th width="20%">Company</th>
            <th width="10%">Deal Number</th>
            <th width="16%">Deal Name</th>
            <th width="6%">Start Date<br />&nbsp;&nbsp;&nbsp;-<br />End Date</th>
            <th width="6%">Deal Amount</th>
            <th width="6%">LS Supplements</th>
            <th width="6%">Add-ons</th>
            <th width="6%">Expected price</th>
            <th width="6%">Allocated budget</th>
            <th width="6%">Expected cost</th>
            <th width="6%">Remaining</th>
            <th width="6%">Waived</th>
        </thead>
        <tbody>
            {foreach $all_deals as $deal}
            <tr style="overflow-wrap: break-word;">
                <td>{TemplateHelper::uiCleanseHTMLNewlineAndTabs($deal['company_name'])}</td>
                <td><a href="{urlFor name="deal_id_report" options="deal_id.{$deal['deal_id']}"}" target="_blank">{$deal['deal_id']}</a></td>
                <td>{TemplateHelper::uiCleanseHTMLNewlineAndTabs($deal['deal_name'])}</td>
                <td>{substr($deal['start_date'], 0, 10)}<br />&nbsp;&nbsp;&nbsp;-<br />{substr($deal['expiration_date'], 0, 10)}</td>
                <td>${round($deal['deal_total'], 2)}</td>
                <td>${round($deal['deal_partnership'], 2)}</td>
                <td>${round($deal['deal_supplements'], 2)}</td>
                <td>{if !empty($deal['total_expected_price'])}${round($deal['total_expected_price'], 2)}{/if}</td>
                <td>{if !empty($deal['allocated_budget'])}${round($deal['allocated_budget'], 2)}{/if}</td>
                <td>{if !empty($deal['total_expected_cost'])}${round($deal['total_expected_cost'], 2)}{/if}</td>
                <td>{if !empty($deal['allocated_budget']) && !empty($deal['total_expected_cost'])}${round($deal['allocated_budget'] - $deal['total_expected_cost'], 2)}{/if}</td>
                <td>{if !empty($deal['total_expected_cost_waived'])}${round($deal['total_expected_cost_waived'], 2)}{/if}</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</body>
</html>
