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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function(){
        var table = $('#myTable').DataTable({ "paging": false });
        table.order([0, 'asc'], [1, 'asc'], [2, 'asc'], [3, 'asc'], [4, 'asc']).draw();
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

<h2 style="text-align:center;">Purchase Order Readiness Report</h2>
<a href="{urlFor name="sun_po_errors"}" target="_blank">Sun Purchase Order Errors</a>
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
        <th>Purchase Requisition</th>
        <th>Unit Count</th>
        <th>Unit Rate (Linguist)</th>
        <th>Total</th>
        <th>Completed Date</th>
        <th>Payment Status</th>
    </thead>    
    <tbody>
        {foreach $tasks as $task}
        <tr>
            <td><a href="{urlFor name="user-public-profile" options="user_id.{$task['user_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($task['linguist'])}</a>{if !empty($task['linguist_t_code'])} ({$task['linguist_t_code']}){/if}</td>
            <td><a href="{urlFor name="org-public-profile" options="org_id.{$task['organisation_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($task['name'])}</a></td>
            <td><a href="{urlFor name="project-view" options="project_id.{$task['project_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($task['title'])}</a>{if !empty($task['project_t_code'])} ({$task['project_t_code']}){/if}</td>
            <td>{substr($task['creator_email'], 0, strpos($task['creator_email'], '@'))}</td>
            <td><a href="{urlFor name="task-view" options="task_id.{$task['task_id']}"}" target="_blank">{$task['task_id']}</a></td>
            <td>{$task['type_text']}</td>
            <td>{$task['language_pair']}</td>
            <td>{if !empty($task['deal_id'])}<a href="{urlFor name="deal_id_report" options="deal_id.{$task['deal_id']}"}" target="_blank">{$task['deal_id']}</a>{/if}
                {if $task['total_paid_words'] == 0}<br /><span style="color: red;">Task has No {$task['pricing_and_recognition_unit_text_hours']}</span>{/if}
                {if empty($task['deal_id'])}<br /><span style="color: red;">No HS Deal</span>{/if}
                {if empty($task['project_t_code'])}<br /><span style="color: red;">No Project T-Code</span>
                {elseif !empty($task['pr_created']) && ($task['project_t_code'] != $task['pr_project_t_code'])}<br /><span style="color: red;">Project T-Code Different in Project and Purchase Requisition</span>{/if}
                {if empty($task['purchase_requisition'])}<br /><span style="color: red;">No Purchase Requisition</span>
                {elseif empty($task['pr_created'])}<br /><span style="color: red;">No Matching Purchase Requisition</span>
                {elseif empty($task['approvalStatus'])}<br /><span style="color: red;">Purchase Requisition Not Approved</span>{/if}
                {if empty($task['linguist_t_code'])}<br /><span style="color: red;">No Linguist T-Code</span>{/if}
                {if empty($task['google_drive_link'])}<br /><span style="color: red;">No Linguist Payment Information</span>{/if}
                {if $task['total_paid_words'] != 0 && !empty($task['deal_id']) && !empty($task['project_t_code']) && !empty($task['purchase_requisition']) && !empty($task['approvalStatus']) && !empty($task['linguist_t_code']) && !empty($task['google_drive_link'])}
                    {if $task['task-status_id'] < 4}<span style="color: red;">Task NOT Complete</span>
                    {elseif empty($task['po_created'])}<span style="color: red;">Purchase Order Creation Failure{if $task['purchase_order'] != '0'}; Old format PO Previously Specified: {$task['purchase_order']}{/if}</span>{/if}
                {/if}
            </td>
            <td>{$task['purchase_requisition']}{if !empty($task['pr_total'])}<br />PR Total: ${round($task['pr_total'], 2)}{/if}</td>
            <td>{round($task['total_paid_words'], 2)} {$task['pricing_and_recognition_unit_text_hours']}</td>
            <td>{round($task['unit_rate'], 2)}</td>
            <td>${round($task['total_expected_cost'], 2)}</td>
            <td>{if !empty($task['complete_date']) && $task['task-status_id'] >= 4}{substr($task['complete_date'], 0, 10)}{/if}</td>
            <td>{$task['payment_status']}</td>
        </tr>
        {/foreach}
    </tbody>
</table>

{else}<p class="alert alert-info">No Paid Tasks found</p>{/if}

</body>
</html>
