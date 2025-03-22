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
        table.order([0, 'desc']).draw();
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

<h2 style="text-align:center;">Purchase Requisition Report</h2>
<a href="{urlFor name="sow_report"}" target="_blank">SoW Report</a>
<br />

<table id="myTable" style="overflow-wrap: break-word;" class="container table table-striped">
    <thead>
        <th>PR #</th>
        <th>Creator</th>
        <th>Date</th>
        <th>Approval</th>
        <th>Status</th>
        <th>Total (SUN)</th>
        <th>Total Paid</th>
        <th>Total Unwaived</th>
        <th>Total Complete Unwaived</th>
        <th>Total Waived</th>
        <th>Total PO</th>
        <th>Total PR less POs</th>
    </thead>
    <tbody>
        {foreach $prs as $pr}
        <tr>
            <td>{$pr['purchase_requisition']}</td>
            <td>{$pr['creator']}</td>
            <td>{substr($pr['dateTimeLastUpdated'], 0, 10)}</td>
            <td>{if $pr['approvalStatus'] == 0}Not Approved{/if}{if $pr['approvalStatus'] == 1}Approved{/if}{if $pr['approvalStatus'] == 99}Not Applicable{/if}</td>
            <td>{if $pr['status'] == 0}Open{/if}{if $pr['status'] == 1}1{/if}{if $pr['status'] == 2}Awaiting Approval{/if}{if $pr['status'] == 3}Awaiting Purchasing{/if}{if $pr['status'] == 4}PO Generated{/if}</td>
            <td>${$pr['total']}</td>
            <td>${round($pr['total_tasks_for_pr'] + $pr['total_waived_tasks_for_pr'], 2)}</td>
            <td>${round($pr['total_tasks_for_pr'], 2)}</td>
            <td>${round($pr['total_completed_tasks_for_pr'], 2)}</td>
            <td>${round($pr['total_waived_tasks_for_pr'], 2)}</td>
            <td>${round($pr['total_po'], 2)}</td>
            <td>{if $pr['total_po'] != 0 AND round($pr['total'] - $pr['total_po'], 2) != 0}<strong><span style="color: red">${round($pr['total'] - $pr['total_po'], 2)}</span></strong>{/if}</td>
        {/foreach}
    </tbody>
</table>

</body>
</html>
