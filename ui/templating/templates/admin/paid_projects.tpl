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
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function(){
        $('#myTable').DataTable(
          {
            "paging": false
          }
        );
      });
    </script>
</head>
<body>
    <table id="myTable" style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
        <thead>
            <th width="4%">ID</th>
            <th width="8%">Project</th>
            <th width="6%">Deal ID</th>
            <th width="6%">Allocated Budget</th>
            <th width="6%">Expense (Expected)</th>
            <th width="6%">Expense (Claimed Tasks)</th>
            <th width="6%">Remaining Budget</th>
            <th width="4%">Margin</th>
            <th width="5%">Expense (Completed Tasks)</th>
            <th width="5%">Expense (Ready for payment Tasks)</th>
            <th width="8%">Company Name</th>
            <th width="5%">Deal Name</th>
            <th width="5%">Contract Start Date</th>
            <th width="5%">Contract Expiration Date</th>
            <th width="6%">Deal Amount</th>
            <th width="5%">LS Supplements (core agreement)</th>
            <th width="5%">Supplements (add-ons)</th>
            <th width="5%">Link to Contract</th>
        </thead>
        <tbody>
            {foreach $paid_projects as $paid_project}
            <tr style="overflow-wrap: break-word;">
                <td>{$paid_project['project_id']}</td>
                <td><a href="{urlFor name="project-view" options="project_id.{$paid_project['project_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($paid_project['title'])}</a></td>
                <td>{if $paid_project['deal_id'] > 0}<a href="{urlFor name="deal_id_report" options="deal_id.{$paid_project['deal_id']}"}" target="_blank">{$paid_project['deal_id']}</a>{else}{$paid_project['deal_id']}{/if}</td>
                <td>${round($paid_project['allocated_budget'], 2)}</td>
                <td>${round($paid_project['total_expected_cost'], 2)}</td>
                <td>${round($paid_project['total_expected_cost_claimed'], 2)}</td>
                <td>${round($paid_project['allocated_budget'] - $paid_project['total_expected_cost_claimed'], 2)}</td>
                <td>{if $paid_project['allocated_budget'] > 0}{round((($paid_project['allocated_budget'] - $paid_project['total_expected_cost_claimed'])/$paid_project['allocated_budget'])*100)}%{else}-{/if}</td>
                <td>${round($paid_project['total_expected_cost_complete'], 2)}</td>
                <td>${round($paid_project['total_expected_cost_ready'], 2)}</td>
                <td>{TemplateHelper::uiCleanseHTMLNewlineAndTabs($paid_project['company_name'])}</td>
                <td>{TemplateHelper::uiCleanseHTMLNewlineAndTabs($paid_project['deal_name'])}</td>
                <td>{$paid_project['start_date']}</td>
                <td>{$paid_project['expiration_date']}</td>
                <td>{if isset($paid_project['deal_total'])}${$paid_project['deal_total']}{/if}</td>
                <td>{if isset($paid_project['deal_partnership'])}${$paid_project['deal_partnership']}{/if}</td>
                <td>{if isset($paid_project['deal_supplements'])}${$paid_project['deal_supplements']}{/if}</td>
                <td><a href="{$paid_project['link_to_contract']}" target="_blank">{$paid_project['link_to_contract']}</a></td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</body>
</html>
