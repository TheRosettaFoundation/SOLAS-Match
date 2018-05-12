<!-- Editor Hint: ¿áéíóú -->

{assign var="task_id" value=$task->getId()}

<span class="hidden">
    <div id="siteLocationURL">{Settings::get("site.location")}</div>
    <div id="task_id_for_invites_sent">{$task_id}</div>
    <div id="sesskey">{$sesskey}</div>
</span>

{include file="header.tpl"}

    <h1 class="page-header" style="height: auto">
        <span style="height: auto; width: 750px; overflow-wrap: break-word; display: inline-block;">
            {if $task->getTitle() != ''}
                <a href="{urlFor name="task-view" options="task_id.{$task->getId()}"}" target="_blank">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($task->getTitle())}</a>
            {else}
                {Localisation::getTranslation('common_task')} {$task->getId()}
            {/if}

            <small>
                <strong>
                     -
                    {assign var="type_id" value=$task->getTaskType()}
                    {if $type_id == TaskTypeEnum::SEGMENTATION}
                        <span style="color: {$taskTypeColours[TaskTypeEnum::SEGMENTATION]}">{Localisation::getTranslation('common_segmentation_task')}</span>
                    {elseif $type_id == TaskTypeEnum::TRANSLATION}
                        <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">{Localisation::getTranslation('common_translation_task')}</span>
                    {elseif $type_id == TaskTypeEnum::PROOFREADING}
                        <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">{Localisation::getTranslation('common_proofreading_task')}</span>
                    {elseif $type_id == TaskTypeEnum::DESEGMENTATION}
                        <span style="color: {$taskTypeColours[TaskTypeEnum::DESEGMENTATION]}">{Localisation::getTranslation('common_desegmentation_task')}</span>
                    {/if}
                </strong>
            </small>
        </span>

        <div class="pull-right">
            <a href="{urlFor name="task-alter" options="task_id.$task_id"}" class='pull-right fixMargin btn btn-primary'>
                <i class="icon-wrench icon-white"></i> {Localisation::getTranslation('task_view_edit_task_details')}
            </a>
        </div>
    </h1>

		{include file="task/task.details.tpl"} 

{if !empty($sent_users)}
<table style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
  <thead>
    <th width="15%">Date Sent Invite</th>
    <th width="15%">Date Viewed Task</th>
    <th width="15%">Date Claimed Task</th>
    <th width="15%">Display Name</th>
    <th width="20%">Email</th>
    <th width="20%">Name</th>
  </thead>

  <tbody>
  {foreach $sent_users as $user_row}
    <tr>
      <td><div class="convert_utc_to_local" style="visibility: hidden">{$user_row['date_sent_invite']}</div></td>
      <td><div class="convert_utc_to_local" style="visibility: hidden">{$user_row['date_viewed_task']}</div></td>
      <td><div class="convert_utc_to_local" style="visibility: hidden">{$user_row['date_claimed_task']}</div></td>
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['user_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['display_name'])}</a></td>
      <td>{$user_row['email']}</td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['first_name'])} {TemplateHelper::uiCleanseHTML($user_row['last_name'])}</td>
    </tr>
  {/foreach}
  </tbody>
</table>
{/if}

{if !empty($all_users)}
<p>
  <button onclick="sendEmails(); return false;" class="btn btn-success">
    <i class="icon-list-alt icon-white"></i> Send Invite to Selected Users
  </button>
</p>
<table id="myTable" style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
  <thead>
    <th width="8%">Send Invite?</th>
    <th width="15%">Display Name</th>
    <th width="15%">Email</th>
    <th width="15%">Name</th>
    <th width="10%">Qualification Level</th>
    <th width="13%">Native Language</th>
    <th width="8%">Country</th>
    <th width="8%">Words Delivered (last 3 months)</th>
    <th width="8%">User Liked Tags</th>
  </thead>

  <tbody>
  {foreach $all_users as $user_row}
    <tr>
      <td><input type="checkbox" class="translator_invite" id="{$user_row['user_id']}" /></td>
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['user_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['display_name'])}</a></td>
      <td>{$user_row['email']}</td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['first_name'])} {TemplateHelper::uiCleanseHTML($user_row['last_name'])}</td>
      <td>{$user_row['level']}</td>
      <td>{$user_row['language_name_native']}</td>
      <td>{$user_row['country_name_native']}</td>
      <td>{$user_row['words_delivered']} ({$user_row['words_delivered_last_3_months']})</td>
      <td>{$user_row['user_liked_tags']}</td>
    </tr>
  {/foreach}
  </tbody>
</table>

{else}<p class="alert alert-info">No Users Found</p>{/if}

{include file="footer.tpl"}
