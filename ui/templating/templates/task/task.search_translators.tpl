{include file="header.tpl"}
<!-- Editor Hint: ¿áéíóú -->

{assign var="task_id" value=$task->getId()}
{assign var="type_id" value=$task->getTaskType()}

<span class="hidden">
  <div id="siteLocationURL">{Settings::get("site.location")}</div>
  <div id="task_id_for_invites_sent">{$task_id}</div>
  <div id="sesskey">{$sesskey}</div>
  {assign var="subject" value="New task '{$task->getTitle()}' on TWB Platform"}
  {assign var="body" value="Dear TWB volunteer,\r\n\r\nA new task is available for you on TWB Platform.\r\n\r\n{if !TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}This is a new task type and you cannot claim it as you would do with translation or revision tasks. If you are interested and would like to claim it, please let us know by replying to this message.!!!\r\n\r\n{/if}Task Name: {$task->getTitle()}\r\n{if TaskTypeEnum::$enum_to_UI[$type_id]['source_and_target']}Language Pair: {TemplateHelper::getLanguageAndCountry($task->getSourceLocale())} to {TemplateHelper::getLanguageAndCountry($task->getTargetLocale())}\r\n{else}Target Language: {TemplateHelper::getLanguageAndCountry($task->getTargetLocale())}\r\n{/if}Type: {TaskTypeEnum::$enum_to_UI[$type_id]['type_text']} Task\r\nTask Delivery Date: {$task->getDeadline()}\r\n{TaskTypeEnum::$enum_to_UI[$type_id]['unit_count_text']}: {$task->getWordCount()}\r\n\r\n{if !TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}To learn more about the task and/or claim this task,\r\n{else}To learn more about the task,\r\n{/if}please visit https://twbplatform.org/task/{$task_id}/view\r\n\r\n{if !TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}Do not start translating until you have claimed the translation\r\non the page above.\r\n\r\n{/if}If you have any questions or comments about this task,\r\nyou can leave a message in the TWB Community forum:\r\nhttps://community.translatorswb.org/t/{$discourse_slug}\r\n\r\n{if !TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}{if !empty($other_task_ids)}Additionally, the above task is part of a project that was split into chunks because of its word count.\r\nOther task chunks you could claim are:\r\n{foreach $other_task_ids as $other_task_id}https://twbplatform.org/task/{$other_task_id}/view\r\n{/foreach}\r\n{/if}{/if}Thank you for your contribution and for your continued support!\r\n\r\n"}
  <div id="mailto_subject">{rawurlencode($subject)}</div>
  <div id="mailto_body"   >{rawurlencode($body)}</div>
</span>

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
                    {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                        {if $type_id == $task_type}
                            <span style="color: {$ui['colour']}">{$ui['type_text']} Task</span>
                        {/if}
                    {/foreach}
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

    <h3>Manual Sourcing</h3>
    {if     $any_country == 3}
        Sourcing is currently <strong>loose, ignoring source</strong>: Source users matching target language "{$task->getTargetLocale()->getLanguageCode()}" <strong>irrespective of country</strong><br />
        If you want to source users ignoring source language matching exact "{$task->getTargetLocale()->getLanguageCode()}-{$task->getTargetLocale()->getCountryCode()}" target locale <strong><a href="{urlFor name="task-search_translators_no_source" options="task_id.$task_id"}">click here</a></strong><br />
        {if TaskTypeEnum::$enum_to_UI[$type_id]['sourcing'] != 1}If you want to source users matching exact "{$task->getTargetLocale()->getLanguageCode()}-{$task->getTargetLocale()->getCountryCode()}" target locale <strong><a href="{urlFor name="task-search_translators" options="task_id.$task_id"}">click here</a></strong><br />{/if}
        {if TaskTypeEnum::$enum_to_UI[$type_id]['sourcing'] != 1}If you want to source all users with the target language "{$task->getTargetLocale()->getLanguageCode()}" <strong>irrespective of country</strong> <strong><a href="{urlFor name="task-search_translators_any_country" options="task_id.$task_id"}">click here</a></strong>{/if}
    {elseif $any_country == 2}
        Sourcing is currently <strong>strict, ignoring source</strong>: Source users matching exact "{$task->getTargetLocale()->getLanguageCode()}-{$task->getTargetLocale()->getCountryCode()}" target locale<br />
        If you want to source all users ignoring source language with the target language "{$task->getTargetLocale()->getLanguageCode()}" <strong>irrespective of country</strong> <strong><a href="{urlFor name="task-search_translators_any_country_no_source" options="task_id.$task_id"}">click here</a></strong><br />
        {if TaskTypeEnum::$enum_to_UI[$type_id]['sourcing'] != 1}If you want to source users matching exact "{$task->getTargetLocale()->getLanguageCode()}-{$task->getTargetLocale()->getCountryCode()}" target locale <strong><a href="{urlFor name="task-search_translators" options="task_id.$task_id"}">click here</a></strong><br />{/if}
        {if TaskTypeEnum::$enum_to_UI[$type_id]['sourcing'] != 1}If you want to source all users with the target language "{$task->getTargetLocale()->getLanguageCode()}" <strong>irrespective of country</strong> <strong><a href="{urlFor name="task-search_translators_any_country" options="task_id.$task_id"}">click here</a></strong>{/if}
    {elseif $any_country == 1}
        Sourcing is currently <strong>loose</strong>: Source users matching target language "{$task->getTargetLocale()->getLanguageCode()}" <strong>irrespective of country</strong><br />
        {if TaskTypeEnum::$enum_to_UI[$type_id]['sourcing'] != 0}If you want to source users ignoring source language matching exact "{$task->getTargetLocale()->getLanguageCode()}-{$task->getTargetLocale()->getCountryCode()}" target locale <strong><a href="{urlFor name="task-search_translators_no_source" options="task_id.$task_id"}">click here</a></strong><br />{/if}
        {if TaskTypeEnum::$enum_to_UI[$type_id]['sourcing'] != 0}If you want to source all users ignoring source language with the target language "{$task->getTargetLocale()->getLanguageCode()}" <strong>irrespective of country</strong> <strong><a href="{urlFor name="task-search_translators_any_country_no_source" options="task_id.$task_id"}">click here</a></strong><br />{/if}
        If you want to source users matching exact "{$task->getTargetLocale()->getLanguageCode()}-{$task->getTargetLocale()->getCountryCode()}" target locale <strong><a href="{urlFor name="task-search_translators" options="task_id.$task_id"}">click here</a></strong>
    {else}
        Sourcing is currently <strong>strict</strong>: Source users matching exact "{$task->getTargetLocale()->getLanguageCode()}-{$task->getTargetLocale()->getCountryCode()}" target locale<br />
        {if TaskTypeEnum::$enum_to_UI[$type_id]['sourcing'] != 0}If you want to source users ignoring source language matching exact "{$task->getTargetLocale()->getLanguageCode()}-{$task->getTargetLocale()->getCountryCode()}" target locale <strong><a href="{urlFor name="task-search_translators_no_source" options="task_id.$task_id"}">click here</a></strong><br />{/if}
        {if TaskTypeEnum::$enum_to_UI[$type_id]['sourcing'] != 0}If you want to source all users ignoring source language with the target language "{$task->getTargetLocale()->getLanguageCode()}" <strong>irrespective of country</strong> <strong><a href="{urlFor name="task-search_translators_any_country_no_source" options="task_id.$task_id"}">click here</a></strong><br />{/if}
        If you want to source all users with the target language "{$task->getTargetLocale()->getLanguageCode()}" <strong>irrespective of country</strong> <strong><a href="{urlFor name="task-search_translators_any_country" options="task_id.$task_id"}">click here</a></strong>
    {/if}
    <br /><br />

{if !empty($sent_users)}
<table style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
  <thead>
    <th width="8%">Send Invite?</th>
    <th width="15%">Date Sent Invite</th>
    <th width="15%">Date Viewed Task</th>
    <th width="15%">Date Claimed Task</th>
    <th width="15%">Display Name</th>
    <th width="16%">Email</th>
    <th width="16%">Name</th>
  </thead>

  <tbody>
  {foreach $sent_users as $user_row}
    <tr>
      <td><input type="checkbox" class="translator_invite" id="{$user_row['user_id']}" email="{$user_row['email']}" /></td>
      <td><div class="convert_utc_to_local" style="visibility: hidden">{$user_row['date_sent_invite']}</div></td>
      <td><div {if $user_row['date_viewed_task']  != ''}class="convert_utc_to_local" style="visibility: hidden"{/if}>{$user_row['date_viewed_task']}</div></td>
      <td><div {if $user_row['date_claimed_task'] != ''}class="convert_utc_to_local" style="visibility: hidden"{/if}>{$user_row['date_claimed_task']}</div></td>
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['user_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['display_name'])}</a></td>
      <td>{$user_row['email']}</td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['first_name'])} {TemplateHelper::uiCleanseHTML($user_row['last_name'])}</td>
    </tr>
  {/foreach}
  </tbody>
</table>
{/if}

{if !empty($all_users) || !empty($sent_users)}
<p>
  <a href="" id="mymailto" style="display:none"></a>
  <button onclick="sendEmails(); return false;" class="btn btn-success">
    <i class="icon-list-alt icon-white"></i> Send Invite to Selected Users
  </button>
  {if !empty($all_users)}
  <button onclick="check15More(); return false;" class="btn btn-success">
    <i class="icon-list-alt icon-white"></i> Check the next 15
  </button>
  {/if}
</p>
{/if}
{if !empty($all_users)}
<table id="myTable" style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
  <thead>
    <th width="6%">Send Invite?</th>
    <th width="13%">Display Name</th>
    <th width="17%">Email</th>
    <th width="17%">Name</th>
    <th width="10%">Qualification Level</th>
    <th width="13%">Native Language</th>
    <th width="10%">Country</th>
    <th width="8%">Words Delivered (last 3 months)</th>
    <th width="6%">User Unit Rate</th>
  </thead>

  <tbody>
  {foreach $all_users as $user_row}
    <tr>
      <td><input type="checkbox" class="translator_invite not_sent" id="{$user_row['user_id']}" email="{$user_row['email']}" /></td>
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['user_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['display_name'])}</a></td>
      <td>{$user_row['email']}</td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['first_name'])} {TemplateHelper::uiCleanseHTML($user_row['last_name'])}</td>
      <td>{$user_row['level']}</td>
      <td>{$user_row['language_name_native']}</td>
      <td>{$user_row['country_name_native']}</td>
      <td>{$user_row['words_delivered']} ({$user_row['words_delivered_last_3_months']})</td>
      <td>{if !empty($user_row['unit_rate'])}${/if}{$user_row['unit_rate']}</td>
    </tr>
  {/foreach}
  </tbody>
</table>

{else}<p class="alert alert-info">No Qualified Uninvited Users Found!</p>{/if}

{include file="footer.tpl"}
