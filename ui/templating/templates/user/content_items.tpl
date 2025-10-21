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
    </style>
</head>
<body>
{if !empty($items)}

<h2 style="text-align:center;">Resources</h2>

    {if isset($flash['error'])}
        <p class="alert alert-error">
            {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
        </p>
    {/if}

{if !empty($org_id)}
    <a href="{urlFor name="content_item_org" options="content_id.0|org_id.$org_id"}" target="_blank">Create a new Organisation Resource</a>
{else}
    <a href="{urlFor name="content_item" options="content_id.0"}" target="_blank">Create a new Resource</a>
{/if}

<table id="myTable" style="overflow-wrap: break-word;" class="container table table-striped">
    <thead>
        <th>ID</th>
        <th>Title</th>
        <th>Type</th>
        <th>Scope</th>
        <th>Highlight</th>
        <th>Published</th>
        <th>Sort Order</th>
        <th>Number of Views</th>
        <th>Organization</th>
        <th>Admin</th>
        <th>Images</th>
        <th>Attachments</th>
        <th>Projects</th>
    </thead>    
    <tbody>
        {foreach $items as $item}
        <tr>
            <td>
            {if !empty($item['owner_org_id'])}
            <a href="{urlFor name="content_item_org" options="content_id.{$item['id']}|org_id.{$item['owner_org_id']}"}" target="_blank">{$item['id']}</a>
            {else}
            <a href="{urlFor name="content_item" options="content_id.{$item['id']}"}" target="_blank">{$item['id']}</a>
            {/if}
            </td>

            <td>{$item['title']|escape:'html':'UTF-8'}</td>

            <td>
            {if $item['type'] == 1}TWB Article{/if}
            {if $item['type'] == 2}Newsletter{/if}
            {if $item['type'] == 3}Event{/if}
            {if $item['type'] == 4}Resource{/if}
            {if $item['type'] == 5}Organization{/if}
            {if $item['type'] == 6}Project{/if}
            </td>

            <td>
            {if $item['scope'] == 1}TWB Resource{/if}
            {if $item['scope'] == 2}Public Resource{/if}
            </td>

            <td>
            {if $item['highlight'] == 1}Highlight{/if}
            </td>

            <td>
            {if $item['published'] == 1}Published{/if}
            </td>

            <td>{$item['sorting_order']}</td>

            <td>{$item['number_of_views']}</td>

            <td>{$item['name']|escape:'html':'UTF-8'}</td>


            <td>{$item['email']}</td>

            <td>{foreach $item['image_ids'] as $image_id}{if !empty($image_id)}
                {if !empty($item['owner_org_id'])}
                    <a href="{urlFor name="download_attachment_org" options="org_id.{$item['owner_org_id']}|content_id.{$item['id']}|is_image.1|sorting_order.$image_id"}">{$image_id},</a>
                {else}
                    <a href="{urlFor name="download_attachment" options="content_id.{$item['id']}|is_image.1|sorting_order.$image_id"}">{$image_id},</a>
                {/if}
            {/if}{/foreach}</td>

            <td>{foreach $item['attachment_ids'] as $attachment_id}{if !empty($attachment_id)}
                {if !empty($item['owner_org_id'])}
                    <a href="{urlFor name="download_attachment_org" options="org_id.{$item['owner_org_id']}|content_id.{$item['id']}|is_image.0|sorting_order.$attachment_id"}">{$attachment_id},</a>
                {else}
                    <a href="{urlFor name="download_attachment" options="content_id.{$item['id']}|is_image.0|sorting_order.$attachment_id"}">{$attachment_id},</a>
                {/if}
            {/if}{/foreach}</td>

            <td>{foreach $item['project_ids'] as $project_id}{if !empty($project_id)}
                    <a href="{urlFor name="project-view" options="project_id.$project_id"}" target="_blank">{$project_id},</a>
            {/if}{/foreach}</td>
        </tr>
        {/foreach}
    </tbody>
</table>

{else}<p class="alert alert-info">No Resources found</p>{/if}

</body>
</html>
