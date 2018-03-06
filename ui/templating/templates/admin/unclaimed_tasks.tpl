<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" >

<head>
    <!-- Editor Hint: ¿áéíóú -->
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
    <th width="17%">Partner</th>
    <th width="18%">Task Title</th>
    <th width="5%">Words</th>
    <th width="10%">Task Type</th>
    <th width="12%">Creator Email</th>
    <th width="15%">Created Time</th>
    <th width="15%">Status</th>
    <th width="8%">URL</th>
  </thead>

  <tbody>
  {foreach $all_users as $user_row}

    <tr>
      <td><a href="{urlFor name="org-public-profile" options="org_id.{$user_row['org_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['org_name'])}</a></td>
      <td><a href="{urlFor name="task-view" options="task_id.{$user_row['task_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['task_title'])}</a></td>
      <td>{$user_row['word_count']}</td>
      <td>{$user_row['task_type_text']}</td>
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['creator_id']}"}" target="_blank">{$user_row['creator_email']}</a></td>
      <td>{$user_row['created_time']}</td>
      <td>{$user_row['status']}</td>
      <td>{if !empty($user_row['matecat_url'])}<a href="{$user_row['matecat_url']}" target="_blank">{$user_row['matecat_langpair_or_blank']}</a>{else}$user_row['language_pair']{/if}</td>
    </tr>

  {/foreach}
  </tbody>

</table>

{else}<p class="alert alert-info">No Users</p>{/if}

</body>
</html>
