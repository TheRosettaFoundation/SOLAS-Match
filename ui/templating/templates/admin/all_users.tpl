{include file='header.tpl'}

<div class='page-header'><h1>All Users</h1></div>

{if isset($all_users) && count($all_users) > 0}

<div class="well">
<table width="100%" style="overflow-wrap: break-word;" class="table table-striped">
  <thead>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Biography</th>
    <th>Language</th>
    <th>City</th>
    <th>Country</th>
    <th>Created</th>
  </thead>

  <tbody>
  {foreach $all_users as $user_row}

    <tr>
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['id']}"}">{$user_row['id']}</a></td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['first_name'])} {TemplateHelper::uiCleanseHTML($user_row['last_name'])}</td>
      <td>{$user_row['email']}</td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['biography'])}</td>
      <td>{$user_row['native_language']} {$user_row['native_country']}</td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['city'])}</td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['country'])}</td>
      <td>{$user_row['created_time']}</td>
    </tr>

  {/foreach}
  </tbody>

</table>
</div>

{else}<p class="alert alert-info">No Users</p>{/if}

{include file='footer.tpl'}
