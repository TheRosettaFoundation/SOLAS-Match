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
        table.order([9, 'desc'], [0, 'asc'], [4, 'desc'], [7, 'asc']).draw();
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
{if !empty($tasks)}

<h2 style="text-align:center;">SoW Linguist Report</h2>
<a href="{urlFor name="sow_report"}" target="_blank">SoW Report</a>
<br />

<table id="myTable" style="overflow-wrap: break-word;" class="container table table-striped">
    <thead>
        <th>Linguist</th>
        <th>Billing Country</th>
        <th>Link to the Documentation</th>
        <th>Total</th>
        <th>Invoice Type</th>
        <th>Invoice Number</th>
        <th>Invoice Name</th>
        <th>Status (filled by Finance)</th>
        <th>Processed?</th>
        <th>Invoice Date</th>
        {if $roles&($SITE_ADMIN + 128)}
        <th></th>
        {/if}
    </thead>
    <tbody>
        {foreach $tasks as $task}
        <tr>
            <td><a href="{urlFor name="user-public-profile" options="user_id.{$task['user_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($task['linguist'])}</a></td>
            <td>{$task['country']}</td>
            <td><a href="{$task['google_drive_link']}"}" target="_blank">Link to Documentation</a></td>
            <td>${round($task['total_expected_cost'], 2)}</td>
            <td>{if !is_null($task['status'])}{if $task['status']&1}Draft{else}Invoice{/if}{/if}</td>
            <td>{if !is_null($task['invoice_number'])}<a href="{urlFor name="get-invoice" options="invoice_number.{$task['invoice_number']}"}" target="_blank">{if $task['status']&1}DRAFT{else}TWB{/if}-{str_pad($task['invoice_number'], 4, '0', STR_PAD_LEFT)}</a>{/if}</td>
            <td>{if !empty($task['google_id'])}<a href="https://drive.google.com/file/d/{$task['google_id']}/view" target="_blank">{$task['filename']}</a>{else}{$task['filename']}{/if}</td>
            <td>
                {if is_null($task['status'])}
                {elseif $task['status']&4}Bounced
                {elseif $task['status']&2}Paid
                {/if}
            </td>
            <td>{if $task['processed'] > 0}Yes{/if}</td>
            <td>{if !empty($task['invoice_date'])}{$task['invoice_date']}{else}None{/if}</td>
            {if $roles&($SITE_ADMIN + 128)}
            <td>
                {if !is_null($task['status']) && !($task['status']&2)}
                    <form>
                        <input type="hidden" class="invoice_number" name="invoice_number" value="{$task['invoice_number']}" />

                        <button type="button" class="btn btn-success mark_paid_button" name="mark_paid_button">
                            <i class="icon-check icon-white"></i> Mark Paid
                        </button>
                        <p style="margin-bottom: 5px;"></p>
                        <button type="button" class="btn btn-danger revoke_button" name="revoke_button">
                            <i class="icon-ban-circle icon-white"></i> Revoke
                        </button>
                        {if isset($sesskey)}<input type="hidden" class="sesskey" name="sesskey" value="{$sesskey}" />{/if}
                    </form>
                {/if}
                {if !is_null($task['status']) && $task['status']&2}
                    <form>
                        <input type="hidden" class="invoice_number" name="invoice_number" value="{$task['invoice_number']}" />

                        <button type="button" class="btn btn-danger mark_bounced_button" name="mark_bounced_button">
                            <i class="icon-ban-circle icon-white"></i> Mark Bounced
                        </button>
                        {if isset($sesskey)}<input type="hidden" class="sesskey" name="sesskey" value="{$sesskey}" />{/if}
                    </form>
                {/if}
            </td>
            {/if}
        </tr>
        {/foreach}
    </tbody>
</table>

{else}<p class="alert alert-info">No Paid Tasks found</p>{/if}

<script>
const mark_paid_buttons = document.querySelectorAll("form .mark_paid_button");

async function set_invoice_paid({ invoice_number, sesskey }) {
    let url = `/set_invoice_paid/${ invoice_number }/`;
    const key = { sesskey };
    try {
        const response = await fetch(url, {
            method: "POST",
            body: new URLSearchParams(key),
        });

        if (!response.ok) {
            throw new Error("error");
        }
    } catch (error) {
        console.error(error);
    }
}

const mark_paid_buttons_array = [...mark_paid_buttons];

if (mark_paid_buttons_array.length > 0) {
    mark_paid_buttons_array.forEach(function (curr, index, mark_paid_buttons_array) {
        let codes = {};
        curr.addEventListener("click", function (e) {
            e.preventDefault();

          if (confirm("Are you sure you want to mark this invoice as paid?")) {
            let parent = curr.parentElement;
            let invoice_number = parent.querySelector(".invoice_number").value;
            let mark_paid_button = parent.querySelector(".mark_paid_button");
            let revoke_button    = parent.querySelector(".revoke_button");
            mark_paid_button.disabled = true;
            revoke_button.disabled = true;

            let sesskey = parent.querySelector(".sesskey").value;
            codes = {
                invoice_number,
                sesskey,
            };

            set_invoice_paid(codes);
          }
        });
    });
}


const mark_bounced_buttons = document.querySelectorAll("form .mark_bounced_button");

async function set_invoice_bounced({ invoice_number, sesskey }) {
    let url = `/set_invoice_bounced/${ invoice_number }/`;
    const key = { sesskey };
    try {
        const response = await fetch(url, {
            method: "POST",
            body: new URLSearchParams(key),
        });

        if (!response.ok) {
            throw new Error("error");
        }
    } catch (error) {
        console.error(error);
    }
}

const mark_bounced_buttons_array = [...mark_bounced_buttons];

if (mark_bounced_buttons_array.length > 0) {
    mark_bounced_buttons_array.forEach(function (curr, index, mark_bounced_buttons_array) {
        let codes = {};
        curr.addEventListener("click", function (e) {
            e.preventDefault();

          if (confirm("Are you sure you want to mark this invoice as bounced?")) {
            let parent = curr.parentElement;
            let invoice_number = parent.querySelector(".invoice_number").value;
            let mark_bounced_button = parent.querySelector(".mark_bounced_button");
            mark_bounced_button.disabled = true;

            let sesskey = parent.querySelector(".sesskey").value;
            codes = {
                invoice_number,
                sesskey,
            };

            set_invoice_bounced(codes);
          }
        });
    });
}


const revoke_buttons = document.querySelectorAll("form .revoke_button");

async function set_invoice_revoked({ invoice_number, sesskey }) {
    let url = `/set_invoice_revoked/${ invoice_number }/`;
    const key = { sesskey };
    try {
        const response = await fetch(url, {
            method: "POST",
            body: new URLSearchParams(key),
        });

        if (!response.ok) {
            throw new Error("error");
        }
    } catch (error) {
        console.error(error);
    }
}

const revoke_buttons_array = [...revoke_buttons];

if (revoke_buttons_array.length > 0) {
    revoke_buttons_array.forEach(function (curr, index, revoke_buttons_array) {
        let codes = {};
        curr.addEventListener("click", function (e) {
            e.preventDefault();

          if (confirm("Are you sure you want to revoke (delete from reports) this invoice? It can be regenerated later.")) {
            let parent = curr.parentElement;
            let invoice_number = parent.querySelector(".invoice_number").value;
            let mark_paid_button = parent.querySelector(".mark_paid_button");
            let revoke_button    = parent.querySelector(".revoke_button");
            mark_paid_button.disabled = true;
            revoke_button.disabled = true;

            let sesskey = parent.querySelector(".sesskey").value;
            codes = {
                invoice_number,
                sesskey,
            };

            set_invoice_revoked(codes);
          }
        });
    });
}
</script>
</body>
</html>
