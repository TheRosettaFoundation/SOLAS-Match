{include file='header.tpl'}

<div class='page-header'><h1>All Users</h1></div>

{if isset($all_users) && count($all_users) > 0}

<table style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
  <thead>
    <th width="5%">ID</th>
    <th width="10%">Name</th>
    <th width="15%">Email</th>
    <th width="40%">Biography</th>
    <th width="9%">Language</th>
    <th width="7%">City</th>
    <th width="7%">Country</th>
    <th width="7%">Created</th>
  </thead>

  <tbody>
  {foreach $all_users as $user_row}

    <tr>
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['id']}"}" target="_blank">{$user_row['id']}</a></td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['first_name'])} {TemplateHelper::uiCleanseHTML($user_row['last_name'])}</td>
      <td>{$user_row['email']}</td>
      <td>{TemplateHelper::uiCleanseHTMLNewlineAndTabs($user_row['biography'])}</td>
      <td>{$user_row['native_language']}<br />{$user_row['native_country']}</td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['city'])}</td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['country'])}</td>
      <td>{$user_row['created_time']}</td>
    </tr>

  {/foreach}
  </tbody>

</table>

{else}<p class="alert alert-info">No Users</p>{/if}

{include file='footer.tpl'}
