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
{if !empty($pos)}

<h2 style="text-align:center;">All Paid Projects (with at least one paid task) for Hubspot Deal ID: {$pos[0]['deal_id']}</h2>
<br />

<table id="myTable0" style="overflow-wrap: break-word;" class="container table table-striped">
    <thead>
        <th width="15%">Company Name</th>
        <th width="15%">Deal Name</th>
        <th width="12%">Contract Start Date</th>
        <th width="12%">Contract Expiration Date</th>
        <th width="10%">Deal Amount</th>
        <th width="12%">LS Supplements (core agreement)</th>
        <th width="12%">Supplements (add-ons)</th>
        <th width="12%">Link to Contract</th>
    </thead>
    <tbody>
        <tr>
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

<table id="myTable1" style="overflow-wrap: break-word;" class="container table table-striped">
    <thead>
        <th>Total Price</th>
        <th>Total Allocated Budget for Projects</th>
        <th>Total Expected Cost (from Total below)</th>
        <th>Total Words</th>
        <th>Total Hours</th>
        <th>Total Terms</th>
    </thead>
    <tbody>
        <tr>
            <td>${round($pos[0]['total_total_expected_price'], 2)}</td>
            <td>${round($pos[0]['total_allocated_budget'], 2)}</td>
            <td>${round($pos[0]['total_total_expected_cost'], 2)}</td>
            <td>{round($pos[0]['total_paid_words_only_words'], 2)}</td>
            <td>{round($pos[0]['total_paid_words_only_hours'], 2)}</td>
            <td>{round($pos[0]['total_paid_words_only_terms'], 2)}</td>
        </tr>
    </tbody>
</table>

<table id="myTable" style="overflow-wrap: break-word;" class="container table table-striped">
    <thead>
        <th>Linguist</th>
        <th>Service Type</th>
        <th>PO #</th>
        <th>Amount</th>
        <th>Total</th>
        <th>Date Submitted</th>
        <th>Project & Task</th>
        <th>Language Pair</th>
        <th>Status</th>
        <th>Source Units</th>
        <th>Launch Date</th>
        <th>Deadline</th>
    </thead>    
    <tbody>
        {foreach $pos as $po}
        <tr>
            <td><a href="{urlFor name="user-public-profile" options="user_id.{$po['user_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($po['linguist'])}</a></td>
            <td>{$po['type_text']}</td>
            <td>{$po['purchase_order']}<br />{if !empty($po['po_total'])}${$po['po_total']}{/if}{if !empty($po['po_status']) && in_array($po['po_status'], ['Completed'])}<i class="fa fa-check-circle-o" style="font-size: 15px !important; padding:0 !important; width:12px !important; margin-left:2px; display: inline-block !important;"></i>{/if}</td>
            <td>{round($po['total_paid_words'], 2)} {$po['pricing_and_recognition_unit_text_hours']} at ${$po['unit_rate']}</td>
            <td>${round($po['total_expected_cost'], 2)}</td>
            <td>{if !empty($po['po_creation_date'])}{substr($po['po_creation_date'], 0, 10)}{/if}</td>
            <td><i><a href="{urlFor name="project-view" options="project_id.{$po['project_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($po['title'])}</a></i><br /><a href="{urlFor name="task-view" options="task_id.{$po['task_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($po['task_title'])}</a></td>
            <td>{$po['language_pair']}</td>
            <td>{$po['task_status']}</td>
            <td>{$po['source_quantity']} {$po['source_unit_for_later_stats']}</td>
            <td>{substr($po['created-time'], 0, 10)}</td>
            <td>{substr($po['deadline'],  0, 16)}</td>
        </tr>
        {/foreach}
    </tbody>
</table>
{else}<p class="alert alert-info">No Projects with that Deal ID</p>{/if}

{else}<p class="alert alert-info">Deal ID not found</p>{/if}

</body>
</html>
