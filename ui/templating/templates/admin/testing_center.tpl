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

{if !empty($all_users)}

<table style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
  <thead>
    <th width="12%">Task Title</th>
    <th width="8%">Type</th>
    <th width="8%">Status</th>
    <th width="11%">Claimant Email</th>
    <th width="10%">Display Name</th>
    <th width="7%">Accuracy</th>
    <th width="6%">Fluency</th>
    <th width="6%">Terminology</th>
    <th width="6%">Style</th>
    <th width="6%">Design</th>
    <th width="20%">Feedback</th>
  </thead>

  <tbody>
  {foreach $all_users as $user_row}

    <tr>
      <td><a href="{urlFor name="task-view" options="task_id.{$user_row['task_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['task_title'])}</a></td>
      <td>{$user_row['task_type']}</td>
      <td>{$user_row['task_status']}</td>
      <td>{$user_row['user_email']}</td>
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['user_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['display_name'])}</a></td>
      <td>{$user_row['accuracy']}</td>
      <td>{$user_row['fluency']}</td>
      <td>{$user_row['terminology']}</td>
      <td>{$user_row['style']}</td>
      <td>{$user_row['design']}</td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['comment'])}</td>
    </tr>

  {/foreach}
  </tbody>

</table>

{else}<p class="alert alert-info">No Tasks</p>{/if}

</body>
</html>
