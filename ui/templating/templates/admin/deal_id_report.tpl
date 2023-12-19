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
</head>
<body>
{if !empty($pos)}

<table id="myTable0" style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
    <thead>
        <th width="10%">HubSpot Deal ID</th>
        <th width="10%">Company Name</th>
        <th width="10%">Deal Name</th>
        <th width="12%">Contract Start Date</th>
        <th width="12%">Contract Expiration Date</th>
        <th width="10%">Deal Amount</th>
        <th width="12%">LS Supplements (core agreement)</th>
        <th width="12%">Supplements (add-ons)</th>
        <th width="12%">Link to Contract</th>
    </thead>
    <tbody>
        <tr>
            <td>{$pos[0]['deal_id']}</td>
            <td>{TemplateHelper::uiCleanseHTMLNewlineAndTabs($pos[0]['company_name'])}</td>
            <td>{TemplateHelper::uiCleanseHTMLNewlineAndTabs($pos[0]['deal_name'])}</td>
            <td>{$pos[0]['start_date']}</td>
            <td>{$pos[0]['expiration_date']}</td>
            <td>${$pos[0]['deal_total']}</td>
            <td>${$pos[0]['deal_partnership']}</td>
            <td>${$pos[0]['deal_supplements']}</td>
            <td><a href="{$pos[0]['link_to_contract']}" target="_blank">contract</a></td>
        </tr>
    </tbody>
</table>

{if !empty($pos[0]['project_id'])}

<table id="myTable1" style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
    <thead>
        <th width="50%">Total Allocated Budget for Projects</th>
        <th width="50%">Total Expected Cost (from TOTAL below)</th>
    </thead>
    <tbody>
        <tr>
            <td>${round($pos[0]['total_allocated_budget'], 2)}</td>
            <td>${round($pos[0]['total_total_expected_cost'], 2)}</td>
        </tr>
    </tbody>
</table>

<table id="myTable" style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
    <thead>
        <th>Linguist</th>
        <th>Service Type</th>
        <th>PO #</th>
        <th>PO Approved</th>
        <th>Amount</th>
        <th>Linguists' Rate</th>
        <th>TOTAL</th>
        <th>Date Submitted</th>
        <th>Project</th>
        <th>File Name</th>
        <th>Language Pair</th>
        <th>Status</th>
        <th>Source Units</th>
        <th>Translation Launch Date</th>
        <th>Estimated Translation deadline</th>
        <th>Translation Delivery Date</th>
        <th>PO Supplier</th>
        <th>PO Total</th>
    </thead>    
    <tbody>
        {foreach $pos as $po}
        <tr>
            <td><a href="{urlFor name="user-public-profile" options="user_id.{$po['user_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($po['linguist'])}</a></td>
            <td>{$po['type_text']}</td>
            <td>{$po['purchase_order']}</td>
            <td>{$po['po_status']}</td>
            <td>{round($po['total_paid_words'], 2)} {$po['pricing_and_recognition_unit_text_hours']}</td>
            <td>${$po['unit_rate']} for {$po['pricing_and_recognition_unit_text_hours']}</td>
            <td>${round($po['total_expected_cost'], 2)}</td>
            <td>{$po['po_creation_date']}</td>
            <td><a href="{urlFor name="project-view" options="project_id.{$po['project_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($po['title'])}</a></td>
            <td><a href="{urlFor name="task-view" options="task_id.{$po['task_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($po['task_title'])}</a></td>
            <td>{$po['language_pair']}</td>
            <td>{$po['task_status']}</td>
            <td>{$po['source_quantity']} {$po['source_unit_for_later_stats']}</td>
            <td>{$po['created-time']}</td>
            <td>{$po['deadline']}</td>
            <td>{$po['complete_date']}</td>
            <td>{$po['po_supplier']}</td>
            <td>{$po['po_total']}</td>
        </tr>
        {/foreach}
    </tbody>
</table>
{else}<p class="alert alert-info">No Projects with that Deal ID</p>{/if}

{else}<p class="alert alert-info">Deal ID not found</p>{/if}

</body>
</html>
