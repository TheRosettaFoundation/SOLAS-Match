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
        table.order([0, 'desc']).draw();
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
    <h2 style="text-align:center;">All Paid Projects (with at least one paid task)</h2>

    <table id="myTable" style="overflow-wrap: break-word;" class="container table table-striped">
        <thead>
            <th width="4%"></th>
            <th width="14%">Project</th>
            <th width="8%">Deal ID</th>
            <th width="8%">Allocated Budget</th>
            <th width="8%">Project Cost</th>
            <th width="8%">Remaining Budget</th>
            <th width="8%">Waived Tasks</th>
            <th width="7%">Status</th>
            <th width="18%">Organization</th>
            <th width="5%">Source Language</th>
            <th width="6%">Project Start&nbsp;&nbsp;Date</th>
            <th width="6%">Project Deadline</th>
        </thead>
        <tbody>
            {foreach $paid_projects as $paid_project}
            <tr style="overflow-wrap: break-word;">
                <td>{$paid_project['project_id']}</td>
                <td><a href="{urlFor name="project-view" options="project_id.{$paid_project['project_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($paid_project['title'])}</a></td>
                <td>{if $paid_project['deal_id'] > 0}<a href="{urlFor name="deal_id_report" options="deal_id.{$paid_project['deal_id']}"}" target="_blank">{$paid_project['deal_id']}</a>{else}{$paid_project['deal_id']}{/if}</td>
                <td>${round($paid_project['allocated_budget'], 2)}</td>
                <td>${round($paid_project['total_expected_cost'], 2)}</td>
                <td>{if round($paid_project['allocated_budget'] - $paid_project['total_expected_cost'], 2) >= 0}${round($paid_project['allocated_budget'] - $paid_project['total_expected_cost'], 2)}{else}<strong><span style="color: red">${round($paid_project['allocated_budget'] - $paid_project['total_expected_cost'], 2)}</span></strong>{/if}</td>
                <td>${round($paid_project['total_expected_cost_waived'], 2)}</td>
                <td>{if $paid_project['status'] == 1}Complete{/if}{if $paid_project['status'] == 2}In Progress{/if}</td>
                <td>{TemplateHelper::uiCleanseHTMLNewlineAndTabs($paid_project['name'])}</td>
                <td>{$paid_project['language_pair']}</td>
                <td>{substr($paid_project['created'], 0, 10)}</td>
                <td>{substr($paid_project['deadline'], 0, 10)}</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</body>
</html>
