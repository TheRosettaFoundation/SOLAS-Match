<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" >
<head>
    <!-- Editor Hint: ¿áéíóú -->
    <meta charset="utf-8" content="application/xhtml+xml" />
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/bootstrap/css/bootstrap.min1.css"/>
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/css/style.1.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/jquery-ui.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/solas2.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css"/>
    <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-1.9.0.js"></script>
    <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function(){
        var table = $('#myTable').DataTable({ "paging": false });
        table.order([9, 'desc'], [0, 'asc'], [4, 'desc'], [7, 'asc']).draw();
      });
    </script>

    <style>
        .container {
            width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>
{if !empty($tasks)}

<h2 style="text-align:center;">SoW Linguist Report</h2>
<a href="{urlFor name="sow_report"}" target="_blank">SoW Report</a>
<br />

<table id="myTable" style="overflow-wrap: break-word;" class="container table table-striped">
    <thead>
        <th>Linguist</th>
        <th>Billing Country</th>
        <th>Link to the Documentation</th>
        <th>Total</th>
        <th>Invoice Type</th>
        <th>Invoice Number</th>
        <th>Invoice Name</th>
        <th>Status (filled by Finance)</th>
        <th>Processed?</th>
        <th>Invoice Date</th>
    </thead>    
    <tbody>
        {foreach $tasks as $task}
        <tr>
            <td><a href="{urlFor name="user-public-profile" options="user_id.{$task['user_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($task['linguist'])}</a></td>
            <td>{$task['country']}</td>
            <td><a href="{$task['google_drive_link']}"}" target="_blank">Link to Documentation</a></td>
            <td>${round($task['total_expected_cost'], 2)}</td>
            <td>{if !is_null($task['status'])}{if $task['status']&1}Draft{else}Invoice{/if}{else}{if $task['proforma']}Draft{else}Invoice{/if}{/if}</td>
            <td>{if !is_null($task['invoice_number'])}<a href="XXXYYYZZZ111222333" target="_blank">{if $task['status']&1}DRAFT{else}TWB{/if}-{str_pad($task['invoice_number'], 4, '0', STR_PAD_LEFT)}</a>{/if}</td>
            <td>{$task['filename']}</td>
            <td>{if !is_null($task['status']) && $task['status']&2}Paid{/if}</td>
            <td>{if $task['processed'] > 0}Yes{/if}</td>
            <td>{if !empty($task['invoice_date'])}{$task['invoice_date']}{else}None{/if}</td>
        </tr>
        {/foreach}
    </tbody>
</table>

{else}<p class="alert alert-info">No Paid Tasks found</p>{/if}

</body>
</html>
