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
[[[
    <th width="5%">Pair</th>
      <td>{$user_row['language_pair']}</td>
]]]
    <th width="9%">Display Name</th>
    <th width="15%">Email</th>
    <th width="15%">Name</th>
    <th width="15%">Task Title</th>
    <th width="8%">Task Type</th>
    <th width="8%">Word Count</th>
    <th width="9%">Date Claimed</th>
    <th width="5%">Codes</th>
    <th width="8%">Source</th>
    <th width="8%">Target</th>
  </thead>

  <tbody>
  {foreach $all_users as $user_row}

    <tr>
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['user_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['display_name'])}</a></td>
      <td>{$user_row['email']}</td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['first_name'])} {TemplateHelper::uiCleanseHTML($user_row['last_name'])}</td>
      <td><a href="{urlFor name="task-view" options="task_id.{$user_row['task_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['task_title'])}</a></td>
      <td>{$user_row['task_type']}</td>
      <td>{$user_row['word_count']}</td>
      <td>{substr($user_row['claimed_time'], 0, 10)}</td>
      <td>{$user_row['language_pair']}</td>
      <td>{$user_row['language_name_source']}</td>
      <td>{$user_row['language_name_target']}</td>
    </tr>

  {/foreach}
  </tbody>

</table>

{else}<p class="alert alert-info">No Users</p>{/if}

</body>
</html>
