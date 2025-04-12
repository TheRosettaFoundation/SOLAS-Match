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
        table.order([14, 'asc'], [15, 'desc'], [0, 'asc'], [16, 'asc'], [1, 'asc'], [2, 'asc'], [3, 'asc'], [4, 'asc']).draw();
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

{if $claimed}
<h2 style="text-align:center;">SoW Report - Ongoing Paid Tasks</h2>
{elseif $po}
<h2 style="text-align:center;">SoW Report - All Tasks for Purchase Order: {$po}</h2>
{else}
<h2 style="text-align:center;">SoW Report - Completed Paid</h2>
{/if}

{if $claimed}
<a href="{urlFor name="sow_report"}" target="_blank">SoW Report - Completed Paid</a>
{elseif $po}
<a href="{urlFor name="sow_report"}" target="_blank">SoW Report - Completed Paid</a>
{else}
<a href="{urlFor name="sow_report"}?claimed=1" target="_blank">SoW Report - Ongoing Paid Tasks</a>
{/if}
<br />
<a href="{urlFor name="sow_linguist_report"}" target="_blank">SoW Linguist Report</a>
<br />

<table id="myTable" style="overflow-wrap: break-word;" class="container table table-striped">
    <thead>
        <th>Linguist</th>
        <th>Organization</th>
        <th>Project</th>
        <th>Project Officer</th>
        <th>Task ID</th>
        <th>Task Type</th>
        <th>Languages</th>
        <th>Deal #</th>
   <!-- <th>Budget Code</th> -->
        <th>PR #</th>
        <th>Unit Count</th>
        <th>Unit Rate (Linguist)</th>
        <th>Total</th>
        <th>Completed Date</th>
        <th>Payment Status</th>
        <th>Proc<br />essed?</th>
        <th>Invoice Date</th>
        <th>Invoice Status</th>
        <th>Invoice Number</th>
    </thead>    
    <tbody>
        {foreach $tasks as $task}
        {if (!$claimed && !$po && !$pr && $task['completed']) || ($claimed && $task['claimed']) || ($po && $po==$task['purchase_order']) || ($pr && $pr==$task['purchase_requisition'])}
        <tr>
            <td><a href="{urlFor name="user-public-profile" options="user_id.{$task['user_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($task['linguist'])}</a></td>
            <td><a href="{urlFor name="org-public-profile" options="org_id.{$task['organisation_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($task['name'])}</a></td>
            <td><a href="{urlFor name="project-view" options="project_id.{$task['project_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($task['title'])}</a></td>
            <td>{substr($task['creator_email'], 0, strpos($task['creator_email'], '@'))}</td>
            <td><a href="{urlFor name="task-view" options="task_id.{$task['task_id']}"}" target="_blank">{$task['task_id']}</a></td>
            <td>{$task['type_text']}</td>
            <td>{$task['language_pair']}</td>
            <td>{if !empty($task['deal_id'])}<a href="{urlFor name="deal_id_report" options="deal_id.{$task['deal_id']}"}" target="_blank">{$task['deal_id']}</a>{/if}</td>
       <!-- <td>{$task['budget_code']}</td> -->
            <td>
                {if !empty($task['purchase_requisition'])}<a href="{urlFor name="sow_report"}?pr={$task['purchase_requisition']}" target="_blank">{$task['purchase_requisition']}</a>{else}<span style="color: red;">No PR</span>{/if}
                <br />
                {if !empty($task['purchase_order'])}<a href="{urlFor name="sow_report"}?po={$task['purchase_order']}" target="_blank">{$task['purchase_order']}</a>{else}{$task['purchase_order']}{/if}
                {if !empty($task['total'])}<br />Total: ${round($task['total'], 2)}{/if}
                {if !empty($task['po_status'])}{if strpos($task['purchase_order'], 'TO-') === false}<br />{if $task['po_status'] == 'Completed' || $task['po_status'] == 'Approved'}{$task['po_status']}{else}<span style="color: red;">{$task['po_status']}, Not Completed</span>{/if}{/if}{else}<br /><span style="color: red;">No PO</span>{/if}
                {if !empty($task['approver_mail'])}<br />{substr($task['approver_mail'], 0, strpos($task['approver_mail'], '@'))}{/if}
                {if $task['total_paid_words'] == 0}<br /><span style="color: red;">Task has No {$task['pricing_and_recognition_unit_text_hours']}</span>{/if}
                {if empty($task['linguist_t_code'])}<br /><span style="color: red;">No Linguist T-Code</span>{/if}
                {if empty($task['google_drive_link'])}<br /><span style="color: red;">No Linguist Payment Information</span>{/if}
                {if empty($task['deal_id'])}<br /><span style="color: red;">No HS Deal</span>{/if}
           </td>
            <td>{round($task['total_paid_words'], 2)} {$task['pricing_and_recognition_unit_text_hours']}</td>
            <td>{round($task['unit_rate'], 2)}</td>
            <td>${round($task['total_expected_cost'], 2)}</td>
            <td>{if !empty($task['complete_date']) && $task['completed']}{substr($task['complete_date'], 0, 10)}{/if}</td>
            <td>{$task['payment_status']}</td>
            <td>{if $task['processed'] > 0}Yes{/if}</td>
            <td>{if !empty($task['invoice_date'])}{$task['invoice_date']}{else}None{/if}</td>
            <td>
                {if is_null($task['status'])}
                {elseif $task['status'] == 0}0-Invoice
                {elseif $task['status'] == 1}1-Draft
                {elseif $task['status'] == 4}2-Invoice Bounced
                {elseif $task['status'] == 5}3-Draft Bounced
                {elseif $task['status'] == 6}2-Invoice Bounced
                {elseif $task['status'] == 7}3-Draft Bounced
                {elseif $task['status'] == 2}4-Invoice Paid
                {elseif $task['status'] == 3}5-Draft Paid
                {/if}
            </td>
            <td>{if $task['invoice_number'] > 0}<a href="{urlFor name="get-invoice" options="invoice_number.{$task['invoice_number']}"}" target="_blank">TWB-{str_pad($task['invoice_number'], 4, '0', STR_PAD_LEFT)}</a>{/if}</td>
        </tr>
        {/if}
        {/foreach}
    </tbody>
</table>

{else}<p class="alert alert-info">No Paid Tasks found</p>{/if}

</body>
</html>
