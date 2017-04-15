{include file='header.tpl'}

<div class='page-header'><h1>Active Now</h1></div>

{if isset($all_users) && count($all_users) > 0}

<table style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
  <thead>
    <th width="20%">Display Name</th>
    <th width="20%">Email</th>
    <th width="25%">Task Title</th>
    <th width="5%">Task ID</th>
    <th width="25%">Project Title</th>
    <th width="5%">Project ID</th>
  </thead>

  <tbody>
  {foreach $all_users as $user_row}

    <tr>
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['user_id']}"}" target="_blank">{$user_row['display_name']}</a></td>
      <td>{$user_row['email']}</td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['task_title'])}</td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['task_id'])}</td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['project_title'])}</td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['project_id'])}</td>
    </tr>

  {/foreach}
  </tbody>

</table>

{else}<p class="alert alert-info">No Users</p>{/if}

{include file='footer.tpl'}
