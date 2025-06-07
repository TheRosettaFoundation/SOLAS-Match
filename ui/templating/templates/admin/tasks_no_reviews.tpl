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
    <th width="15%">Completed</th>
    <th width="35%">Revision Task</th>
    <th width="35%">Reviser</th>
    <th width="15%">Language Pair</th>
  </thead>

  <tbody>
  {foreach $all_users as $user_row}

    <tr>
      <td>{$user_row['complete_date']}</td>
      <td><a href="{urlFor name="task-view" options="task_id.{$user_row['revise_task_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($user_row['task_title'])}</a></td>
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['reviser_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['reviser_name'])}</a></td>
      <td>{$user_row['language_pair']}</td>
    </tr>

  {/foreach}
  </tbody>

</table>

{else}<p class="alert alert-info">No Users</p>{/if}

</body>
</html>
