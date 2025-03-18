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
        table.order([0, 'asc']).draw();
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
        <th>approvalStatus</th>
        <th>status</th>
        <th>Total</th>
        <th>Total Tasks for PR</th>
        <th>Total Tasks Completed for PR</th>
        <th>Total Tasks Waived for PR</th>
        <th>Total PO</th>
        <th>Total PR less POs</th>
    </thead>
    <tbody>
        {foreach $prs as $pr}
        <tr>
            <td>{$pr['purchase_requisition']}</td>
            <td>{$pr['creator']}</td>
            <td>{substr($pr['dateTimeLastUpdated'], 0, 10)}</td>
            <td>{$pr['approvalStatus']}</td>
            <td>{$pr['status']}</td>
            <td>{$pr['total']}</td>
            <td>${round($pr['total_tasks_for_pr'], 2)}</td>
            <td>${round($pr['total_completed_tasks_for_pr'], 2)}</td>
            <td>${round($pr['total_waived_tasks_for_pr'], 2)}</td>
            <td>${round($pr['total_po'], 2)}</td>
            <td>{if round($pr['total'] - $pr['total_po'], 2) != 0}<strong><span style="color: red">{round($pr['total'] - $pr['total_po'], 2)}</span></strong>{/if}</td>
        {/foreach}
    </tbody>
</table>

</body>
</html>
