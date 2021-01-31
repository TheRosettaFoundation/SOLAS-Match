<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" >

<head>
    <!-- Editor Hint: ¿áéíóú -->
    <meta charset="utf-8" content="application/xhtml+xml" />
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/bootstrap/css/bootstrap.min1.css"/>
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/css/style.1.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/jquery-ui.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/solas1.css"/>
    <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-1.9.0.js"></script>
    <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-ui.js"></script>
</head>

<body>

{if isset($all_users) && count($all_users) > 0}

<table id="myTable" style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
  <thead>
    <th width="10%">ID</th>
    <th width="30%">Title</th>
    <th width="30%">Creator</th>
    <th width="20%">memsource URL</th>
    <th width="10%">Word Count</th>
  </thead>

  <tbody>
  {foreach $all_users as $user_row}

    <tr>
      <td>{$user_row['project_id']}</td>
      <td><a href="{urlFor name="project-view" options="project_id.{$user_row['project_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($user_row['title'])}</a></td>
      {if !empty($user_row['creator_id'])}
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['creator_id']}"}" target="_blank">{$user_row['creator_email']}</a></td>
      {else}
      <td></td>
      {/if}
      <td><a href="https://cloud.memsource.com/web/project2/show/{$user_row['memsource_project_uid']}" target="_blank">memsource Project</td>
      <td>{$user_row['word-count']}</td>
    </tr>

  {/foreach}
  </tbody>

</table>

{else}<p class="alert alert-info">No Users</p>{/if}

</body>
</html>
