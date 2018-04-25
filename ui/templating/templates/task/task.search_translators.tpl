<!-- Editor Hint: ¿áéíóú -->
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
        {assign var="task_id" value=$task->getId()}

        <div class="pull-right">
            <a href="{urlFor name="task-alter" options="task_id.$task_id"}" class='pull-right fixMargin btn btn-primary'>
                <i class="icon-wrench icon-white"></i> {Localisation::getTranslation('task_view_edit_task_details')}
            </a>
        </div>
    </h1>

		{include file="task/task.details.tpl"} 

    <p style="margin-bottom: 40px"/>
==========================================================
Desired Qualification level

Columns...
When sent invite
Has viewed Task?
Has claimed Task?
    <th width="11%">Display Name</th>
    <th width="11%">Email</th>
    <th width="10%">Name</th>
---
Columns...
CHECKBOX
    <th width="11%">Display Name</th>
    <th width="11%">Email</th>
    <th width="10%">Name</th>
    <th width="10%">Qualification Level</th>
code-code
    <th width="8%">Native Language</th>
    <th width="8%">Country</th>
Words Delivered (and 3 months)
User likes tags?


Ordered by (words translated)(claimed task before?)
Not claimed outstanding
Randomly?

{if isset($all_users) && count($all_users) > 0}

<table id="myTable" style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
  <thead>
    <th width="11%">Display Name</th>
    <th width="11%">Email</th>
    <th width="10%">Name</th>
    <th width="14%">Source Language</th>
    <th width="8%">Country</th>
    <th width="12%">Target Language</th>
    <th width="8%">Country</th>
    <th width="10%">Qualification Level</th>
    <th width="8%">Native Language</th>
    <th width="8%">Country</th>
  </thead>

  <tbody>
  {foreach $all_users as $user_row}

    <tr>
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['user_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['display_name'])}</a></td>
      <td>{$user_row['email']}</td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['first_name'])} {TemplateHelper::uiCleanseHTML($user_row['last_name'])}</td>
      <td>{$user_row['language_name_source']}</td>
      <td>{$user_row['country_name_source']}</td>
      <td>{$user_row['language_name_target']}</td>
      <td>{$user_row['country_name_target']}</td>
      <td>{$user_row['level']}</td>
      <td>{$user_row['language_name_native']}</td>
      <td>{$user_row['country_name_native']}</td>
    </tr>

  {/foreach}
  </tbody>

</table>

{else}<p class="alert alert-info">No Users</p>{/if}
==========================================================

{include file="footer.tpl"}
