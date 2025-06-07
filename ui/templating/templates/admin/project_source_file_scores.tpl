<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" >

<head>
    <!-- Editor Hint: ¿áéíóú -->
    <meta charset="utf-8" content="application/xhtml+xml" />
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/bootstrap/css/bootstrap.min1.css"/>
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/css/style.1.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/jquery-ui.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/solas3.css"/>
</head>

<body>

{if isset($all_users) && count($all_users) > 0}

<table id="myTable" style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
  <thead>
    <th width="20%">Project</th>
    <th width="20%">Partner</th>
    <th width="5%">Created</th>
    <th width="5%">Delivered</th>
    <th width="5%">Corrections</th>
    <th width="5%">Grammar</th>
    <th width="5%">Spelling</th>
    <th width="5%">Consistency</th>
    <th width="30%">Comments</th>
  </thead>

  <tbody>
  {foreach $all_users as $user_row}

    <tr>
      <td><a href="{urlFor name="project-view" options="project_id.{$user_row['project_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['title'])}</a></td>
      <td><a href="{urlFor name="org-public-profile" options="org_id.{$user_row['organisation_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['name'])}</a></td>
      <td>{$user_row['created']}</td>
      <td>{$user_row['completed']}</td>
      <td>{$user_row['cor']}</td>
      <td>{$user_row['gram']}</td>
      <td>{$user_row['spell']}</td>
      <td>{$user_row['cons']}</td>
      <td>{TemplateHelper::uiCleanseHTMLNewlineAndTabs($user_row['comments'])}</td>
    </tr>

  {/foreach}
  </tbody>

</table>

{else}<p class="alert alert-info">No Users</p>{/if}

</body>
</html>
