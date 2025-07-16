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
        table.order([0, 'desc']).draw();
      });
    </script>

    <style>
        .container {
            width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }
        .make_left {
            text-align: left !important;
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
        <th>Total</th>
        <th>PR #</th>
    </thead>
    <tbody>
        {foreach $pos as $po}
        <tr>
            <td class="make_left"><a href="{urlFor name="sow_report"}?po={$po['purchase_order']}" target="_blank">{$po['purchase_order']}</a></td>
            <td class="make_left">{$po['supplier']}</td>
            <td class="make_left">{$po['status']}</td>
            <td class="make_left">{$po['creation_date']}</td>
            <td class="make_left">${$po['total']}</td>
            <td class="make_left"><a href="{urlFor name="sow_report"}?pr={$po['purchase_requisition']}" target="_blank">{$po['purchase_requisition']}</a></td>
        {/foreach}
    </tbody>
</table>

</body>
</html>
