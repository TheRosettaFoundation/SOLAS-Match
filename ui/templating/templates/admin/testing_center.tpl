<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" >

<head>
    <meta charset="utf-8" content="application/xhtml+xml" />
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/bootstrap/css/bootstrap.min1.css"/>
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/css/style.1.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/jquery-ui.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/solas3.css"/>
</head>

<body>

{if !empty($all_users)}

<table style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
  <thead>
    <th width="6%">Task</th>
    <th width="6%">Pair</th>
    <th width="6%">Created</th>
    <th width="6%">Deadline</th>
    <th width="6%">Translator</th>
    <th width="6%">Level</th>
    <th width="6%">Status</th>
    <th width="6%">Reviewer</th>
    <th width="6%">Revision Status</th>
    <th width="6%">Accuracy</th>
    <th width="6%">Fluency</th>
    <th width="6%">Terminology</th>
    <th width="6%">Style</th>
    <th width="6%">Design</th>
    <th width="16%">Feedback</th>
  </thead>

  <tbody>
  {foreach $all_users as $user_row}

    <tr>
      <td><a href="{urlFor name="task-view" options="task_id.{$user_row['task_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['task_title'])}</a></td>
      <td>{$user_row['language_pair']}</td>
      <td>{$user_row['created']}</td>
      <td>{$user_row['deadline']}</td>
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['user_id']}"}" target="_blank">{$user_row['user_email']}</a></td>
      <td>{$user_row['level']}</td>
      <td><a href="{urlFor name="task-view" options="task_id.{$user_row['task_id']}"}" target="_blank">{$user_row['task_status']}</a></td>
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['proofreading_task_id']}"}" target="_blank">{$user_row['proofreading_email']}</a></td>
      <td><a href="{urlFor name="task-view" options="task_id.{$user_row['proofreading_task_id']}"}" target="_blank">{$user_row['proofreading_task_status']}</a></td>
      <td><a href="{urlFor name="user-task-reviews" options="task_id.{$user_row['task_id']}"}" target="_blank">{$user_row['accuracy']}</a></td>
      <td><a href="{urlFor name="user-task-reviews" options="task_id.{$user_row['task_id']}"}" target="_blank">{$user_row['fluency']}</a></td>
      <td><a href="{urlFor name="user-task-reviews" options="task_id.{$user_row['task_id']}"}" target="_blank">{$user_row['terminology']}</a></td>
      <td><a href="{urlFor name="user-task-reviews" options="task_id.{$user_row['task_id']}"}" target="_blank">{$user_row['style']}</a></td>
      <td><a href="{urlFor name="user-task-reviews" options="task_id.{$user_row['task_id']}"}" target="_blank">{$user_row['design']}</a></td>
      <td><a href="{urlFor name="user-task-reviews" options="task_id.{$user_row['task_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['comment'])}</a></td>
    </tr>

  {/foreach}
  </tbody>

</table>

{else}<p class="alert alert-info">No Tasks</p>{/if}

</body>
</html>
