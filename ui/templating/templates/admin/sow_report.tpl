<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" >
<head>
    <!-- Editor Hint: ¿áéíóú -->
    <meta charset="utf-8" content="application/xhtml+xml" />
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/bootstrap/css/bootstrap.min1.css"/>
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/css/style.1.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/jquery-ui.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/solas2.css"/>
    <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-1.9.0.js"></script>
    <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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

<h2 style="text-align:center;">SoW Report</h2>
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
        <th>Budget Code</th>
        <th>PO #</th>
        <th>PO Approved By</th>
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
            <td>{$task['creator_email']}</td>
            <td><a href="{urlFor name="task-view" options="task_id.{$task['task_id']}"}" target="_blank">{$task['task_id']}</a></td>
            <td>{$task['type_text']}</td>
            <td>{$task['language_pair']}</td>
            <td>{if !empty($task['deal_id'])}<a href="{urlFor name="deal_id_report" options="deal_id.{$task['deal_id']}"}" target="_blank">{$task['deal_id']}</a>{/if}</td>
            <td>{$task['budget_code']}</td>
            <td>{$task['purchase_order']}</td>
            <td>{$task['approver_mail']}</td>
            <td>{round($task['total_paid_words'], 2)} {$task['pricing_and_recognition_unit_text_hours']}</td>
            <td>{round($task['unit_rate'], 2)}</td>
            <td>${round($task['total_expected_cost'], 2)}</td>
            <td>{if !empty($task['complete_date'])}{substr($task['complete_date'], 0, 10)}{/if}</td>
            <td>{$task['payment_status']}</td>
        </tr>
        {/foreach}
    </tbody>
</table>

{else}<p class="alert alert-info">No un-invoiced Paid Tasks found</p>{/if}

</body>
</html>