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

<h2 style="text-align:center;">Purchase Order Readyness Report</h2>

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
        <th>Unit Count</th>
        <th>Unit Rate (Linguist)</th>
        <th>Total</th>
        <th>Completed Date</th>
        <th>Payment Status</th>
    </thead>    
    <tbody>
        {foreach $tasks as $task}
        <tr>
            <td><a href="{urlFor name="user-public-profile" options="user_id.{$task['user_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($task['linguist'])}</a></td>
            <td><a href="{urlFor name="org-public-profile" options="org_id.{$task['organisation_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($task['name'])}</a></td>
            <td><a href="{urlFor name="project-view" options="project_id.{$task['project_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($task['title'])}</a></td>
            <td>{substr($task['creator_email'], 0, strpos($task['creator_email'], '@'))}</td>
            <td><a href="{urlFor name="task-view" options="task_id.{$task['task_id']}"}" target="_blank">{$task['task_id']}</a></td>
            <td>{$task['type_text']}</td>
            <td>{$task['language_pair']}</td>
            <td>{if !empty($task['deal_id'])}<a href="{urlFor name="deal_id_report" options="deal_id.{$task['deal_id']}"}" target="_blank">{$task['deal_id']}</a>{/if}</td>
<td>NO OR
                {if empty($task['deal_id'])}<br /><span style="color: red;">No HS Deal</span>{/if}
                {if empty($task['project_t_code'])}<br /><span style="color: red;">No Project T-Code</span>{/if}
                {if empty($task['purchase_requisition'])}<br /><span style="color: red;">No Purchase Requisition</span>{/if}
                {if !empty($task['purchase_requisition']) && empty($task['approvalStatus'])}<br /><span style="color: red;">Purchase Requisition Not Approved</span>{/if}
                {if empty($task['linguist_t_code'])}<br /><span style="color: red;">No Linguist T-Code</span>{/if}
                {if empty($task['google_drive_link'])}<br /><span style="color: red;">No Linguist Payment Information</span>{/if}

MISSING HIARARCHY...

{if !empty($task['deal_id']) && !empty($task['project_t_code']) && !empty($task['purchase_requisition']) && !empty($task['approvalStatus']) && !empty($task['linguist_t_code']) && !empty($task['google_drive_link']))}

{if $task['task-status_id'] < 4}<span style="color: red;">Task NOT Complete</span>
{elseif empty($task['po_created'])<span style="color: red;">Purchase Order Creation Failure</span>{/if}


{/if}


 
t.`task-status_id`=4

                {if !empty(tp.purchase_order) &&empty($task['google_drive_link'])}<br /><span style="color: red;">No Linguist Payment Information</span>{/if}
 BUT NOT  0



        spr.,


                {if !empty($task['purchase_order'])}<a href="{urlFor name="sow_report"}?po={$task['purchase_order']}" target="_blank">{$task['purchase_order']}</a>{else}{$task['purchase_order']}{/if}
                {if !empty($task['total'])}<br />Total: ${round($task['total'], 2)}{/if}
                <br /><span style="color: red;">No PO</span>
                {if !empty($task['approver_mail'])}<br />{substr($task['approver_mail'], 0, strpos($task['approver_mail'], '@'))}{/if}
                {if empty($task['google_drive_link'])}<br /><span style="color: red;">No Linguist Payment Information</span>{/if}

[[[[display???....
        pcd.project_t_code,
        pcd.purchase_requisition,
        IFNULL(lpi.linguist_t_code, '') AS linguist_t_code,
        tp.purchase_order,
        spr.approvalStatus,
        IF(t.`task-status_id`=4, 1, 0) AS completed,
]]]]
</td>
            <td>{round($task['total_paid_words'], 2)} {$task['pricing_and_recognition_unit_text_hours']}</td>
            <td>{round($task['unit_rate'], 2)}</td>
            <td>${round($task['total_expected_cost'], 2)}</td>
            <td>{if !empty($task['complete_date']) && $task['completed']}{substr($task['complete_date'], 0, 10)}{/if}</td>
            <td>{$task['payment_status']}</td>
        </tr>
        {/foreach}
    </tbody>
</table>

{else}<p class="alert alert-info">No Paid Tasks found</p>{/if}

</body>
</html>
