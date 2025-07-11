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

<h2 style="text-align:center;">Purchase Order Report</h2>
<a href="{urlFor name="sow_report"}" target="_blank">SoW Report</a>
<br />

<table id="myTable" style="overflow-wrap: break-word;" class="container table table-striped">
    <thead>
        <th>PO #</th>
        <th>Supplier</th>
        <th>Status</th>
        <th>Creation date</th>
        <th>Approver mail</th>
        <th>Approval date</th>
        <th>Total Zahara</th>
        <th>Total Tasks Assigned to PO</th>
        <th>Difference</th>
        <th>Total Tasks Completed for PO</th>
        <th>Total Tasks Waived for PO</th>
    </thead>
    <tbody>
        {foreach $pos as $po}
        <tr>
            <td><a href="{urlFor name="sow_report"}?po={$po['purchase_order']}" target="_blank">{$po['purchase_order']}</a></td>
            <td>{$po['supplier']}</td>
            <td>{$po['status']}</td>
            <td>{$po['creation_date']}</td>
            <td>{$po['approver_mail']}</td>
            <td>{$po['approval_date']}</td>
            <td>{$po['total']} {$po['currency']}</td>
            <td>${round($po['total_tasks_for_po'], 2)}</td>
            <td>{if round($po['total'] - $po['total_tasks_for_po'], 2) != 0}<strong><span style="color: red">{round($po['total'] - $po['total_tasks_for_po'], 2)}</span></strong>{/if}</td>
            <td>${round($po['total_completed_tasks_for_po'], 2)}</td>
            <td>${round($po['total_waived_tasks_for_po'], 2)}</td>
        {/foreach}
    </tbody>
</table>

</body>
</html>
