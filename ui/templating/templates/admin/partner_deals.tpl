<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" >
<head>
    <!-- Editor Hint: ¿áéíóú -->
    <meta charset="utf-8" content="application/xhtml+xml" />
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/bootstrap/css/bootstrap.min1.css"/>
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/css/style.1.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/jquery-ui.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/solas3.css"/>
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

{if !empty($deals)}

<h2 style="text-align:center;">Work Report</h2>
<br />


{foreach $deals as $deal}

<table id="myTable0" style="overflow-wrap: break-word;" class="container table table-striped">
    <thead>
        <th width="30%">Company Name</th>
        <th width="30%">Deal Name</th>
        <th width="15%">Start Date</th>
        <th width="15%">End Date</th>
        <th width="10%">Deal Amount</th>
    </thead>
    <tbody>
        <tr>
            <td>{TemplateHelper::uiCleanseHTMLNewlineAndTabs($deal[0]['name'])}</td>
            <td>{TemplateHelper::uiCleanseHTMLNewlineAndTabs($deal[0]['deal_name'])}</td>
            <td>{substr($deal[0]['start_date'], 0, 10)}</td>
            <td>{substr($deal[0]['expiration_date'], 0, 10)}</td>
            <td>{if !empty($deal[0]['deal_total'])}${$deal[0]['deal_total']}{/if}</td>
        </tr>
    </tbody>
</table>


<table id="myTable1" style="overflow-wrap: break-word;" class="container table table-striped">
    <thead>
        <th>Total Price</th>
        <th>Total Words</th>
        <th>Total Hours</th>
        <th>Total Terms</th>
    </thead>
    <tbody>
        <tr>
            <td>${round($deal[0]['total_expected_price'], 2)}</td>
            <td>{round($deal[0]['total_words'], 2)}</td>
            <td>{round($deal[0]['total_hours'], 2)}</td>
            <td>{round($deal[0]['total_terms'], 2)}</td>
        </tr>
    </tbody>
</table>

<table id="myTable" style="overflow-wrap: break-word;" class="container table table-striped">
    <thead>
        <th>Project</th>
        <th>Task Name</th>
        <th>Service Type</th>
        <th>Task Total</th>
        <th>Language Pair</th>
    </thead>    
    <tbody>
        {foreach $deal as $task}
        <tr>
            <td><a href="{urlFor name="project-view" options="project_id.{$task['project_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($task['project_title'])}</a></td>
            <td><a href="{urlFor name="task-view" options="task_id.{$task['task_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($task['task_title'])}</a></td>
            <td>{$task['type_text']}</td>
            <td>${round($task['expected_price'], 2)}</td>
            <td>{$task['language_pair']}</td>
        </tr>
        {/foreach}
    </tbody>
</table>

{/foreach}
{else}<p class="alert alert-info">No Tasks found</p>{/if}

</body>
</html>
