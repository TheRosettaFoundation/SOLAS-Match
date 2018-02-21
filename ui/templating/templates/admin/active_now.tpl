<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" >

<head>
    <meta charset="utf-8" content="application/xhtml+xml" />
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/bootstrap/css/bootstrap.min1.css"/>
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/css/style.1.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/jquery-ui.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/solas1.css"/>
</head>

<body>

{if isset($all_users) && count($all_users) > 0}

<table style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
  <thead>
    <th width="16%">Display Name</th>
    <th width="20%">Email</th>
    <th width="25%">Task Title</th>
    <th width="7%">Task ID</th>
    <th width="25%">Project Title</th>
    <th width="7%">Project ID</th>
  </thead>

  <tbody>
  {foreach $all_users as $user_row}

    <tr>
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['user_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['display_name'])}</a></td>
      <td>{$user_row['email']}</td>
      <td><a href="{urlFor name="task-view" options="task_id.{$user_row['task_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['task_title'])}</a></td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['task_id'])}</td>
      <td><a href="{urlFor name="project-view" options="project_id.{$user_row['project_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['project_title'])}</a></td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['project_id'])}</td>
    </tr>

  {/foreach}
  </tbody>

</table>

{else}<p class="alert alert-info">No Users</p>{/if}

</body>
</html>
