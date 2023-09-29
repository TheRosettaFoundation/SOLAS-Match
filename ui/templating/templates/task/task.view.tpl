{include file="header.tpl"}

    {assign var="task_id" value=$task->getId()}

    <h1 class="page-header" style="height: auto" >
        <span style="height: auto; width: 750px; overflow-wrap: break-word; display: inline-block;">
            {if $task->getTitle() != ''}
                {TemplateHelper::uiCleanseHTMLNewlineAndTabs($task->getTitle())}
            {else}
                {Localisation::getTranslation('common_task')} {$task->getId()}
            {/if}

            <small>
                <strong>
                     -
                    {assign var="type_id" value=$task->getTaskType()}
                    {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                        {if $type_id == $task_type}
                            <span style="color: {$ui['colour']}">{$ui['type_text']} Task</span>
                        {/if}
                    {/foreach}
                </strong>
            </small>  
        </span>

        <div class="pull-right">
            {if $task->getTaskStatus() == TaskStatusEnum::PENDING_CLAIM && !$is_denied_for_task && !TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}
                <a href="{urlFor name="task-claim-page" options="task_id.$task_id"}" class=" pull-right btn btn-primary claim_btn">
                <i class="icon-share-alt icon-white"></i> {Localisation::getTranslation('task_view_download_task')}</a>&nbsp;&nbsp;
            {/if}
            {if ($roles & ($SITE_ADMIN | $PROJECT_OFFICER | $NGO_ADMIN | $NGO_PROJECT_OFFICER))}
                <a href="{urlFor name="task-alter" options="task_id.$task_id"}" class='pull-right fixMargin btn btn-primary' style="margin-top: -12.5%;margin-right: 45%;">
                    <i class="icon-wrench icon-white"></i> {Localisation::getTranslation('task_view_edit_task_details')}
                </a>
            {/if}
        </div>
    </h1>

    {if $task->getTaskStatus() > TaskStatusEnum::PENDING_CLAIM}
        <p class="alert alert-info">
            {Localisation::getTranslation('task_view_0')}
        </p>
    {elseif $is_denied_for_task && $type_id != TaskTypeEnum::TRANSLATION}
        <p class="alert alert-info">
            Note: You cannot claim this task, because you have previously claimed the matching translation task.
        </p>
    {elseif $is_denied_for_task}
        <p class="alert alert-info">
            Note: You cannot claim this task, because you have previously claimed the matching revision or proofreading task.
        </p>
    {/if}
    
    {if isset($flash['success'])}
        <p class="alert alert-success">
            <strong>{Localisation::getTranslation('common_success')}:</strong> {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}
        </p>
    {/if}

    {if isset($flash['error'])}
        <p class="alert alert-error">
            <strong>{Localisation::getTranslation('common_warning')}:</strong> {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
        </p>
    {/if}
	
	{if ($alsoViewedTasksCount>0)}
    <div class="row"></div>
		<div class="row">
			 <div class="span4 pull-right">
		    	<h3>{Localisation::getTranslation('users_also_viewed')}</h3>
		    	
		    	{if isset($alsoViewedTasks)}
		        <div id="also-viewed-tasks">
		            <div class="ts">
		                {for $count=0 to $alsoViewedTasksCount-1}
		                    {assign var="alsoViewedTask" value=$alsoViewedTasks[$count]}
		                    <div class="ts-task">
		                        {assign var="also_viewed_task_id" value=$alsoViewedTask->getId()}
		                        {assign var="also_viewed_type_id" value=$alsoViewedTask->getTaskType()}
		                        {assign var="also_viewed_status_id" value=$alsoViewedTask->getTaskStatus()}
		                        {assign var="also_viewed_task_title" value=$alsoViewedTask->getTitle()}
		                        <div class="task">
		                            <h2>
                                    <a id="also_viewed_task_{$also_viewed_task_id}" href="{$siteLocation}task/{$also_viewed_task_id}/view">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($also_viewed_task_title)}</a>
		                            </h2>
                                {if TaskTypeEnum::$enum_to_UI[$also_viewed_type_id]['source_and_target']}
		                            <p>
		                                {Localisation::getTranslation('common_from')}: <strong>{TemplateHelper::getLanguageAndCountryNoCodes($alsoViewedTask->getSourceLocale())}</strong>
		                            </p>
                                {/if}
		                            <p>
		                            	{Localisation::getTranslation('common_to')}: <strong>{TemplateHelper::getLanguageAndCountryNoCodes($alsoViewedTask->getTargetLocale())}</strong>
		                            </p>
		                            <div>
		                            	<p>
			                            	<span class="label label-info" style="background-color:rgb(218, 96, 52);">{$taskStatusTexts[$also_viewed_status_id]}</span>
			                            	&nbsp;|&nbsp;
                                    <span class="label label-info" style="background-color: {TaskTypeEnum::$enum_to_UI[$also_viewed_type_id]['colour']}">{TaskTypeEnum::$enum_to_UI[$also_viewed_type_id]['type_text_short']}</span>
											&nbsp;|&nbsp;
											{if $alsoViewedTask->getWordCount()}
                                          <span class="label label-info" style="background-color:rgb(57, 165, 231);">{$alsoViewedTask->getWordCount()} {TaskTypeEnum::$enum_to_UI[$also_viewed_type_id]['unit_count_text_short']}</span>
			                                {/if}
		                                </p>
		                            </div>
		                            <p>
                                    Due by <strong><span class="convert_utc_to_local_deadline" style="display: inline-block; visibility: hidden">{$deadline_timestamps[$also_viewed_task_id]}</span></strong>
		                            </p>
                                <p id="also_viewed_parents_{$also_viewed_task_id}">{TemplateHelper::uiCleanseNewlineAndTabs($projectAndOrgs[$also_viewed_task_id])}</p>
		                        </div>
		                    </div>
		                {/for}
		            </div>
		        </div>
				{/if}
		    	
		    </div>
			<div class="pull-left" style="max-width: 70%;">
	{/if}
		
		
		    {include file="task/task.details.tpl"} 

        {if ($roles & ($SITE_ADMIN | $PROJECT_OFFICER | $NGO_ADMIN | $NGO_PROJECT_OFFICER)) && $task->getTaskStatus() < TaskStatusEnum::IN_PROGRESS}
            <div class="well">
            <table><tr>
              <td>
                <form id="assignTaskToUserForm" method="post" action="{urlFor name="task-view" options="task_id.$task_id"}" onsubmit="return confirm('{Localisation::getTranslation("task_view_assign_confirmation")}');">
                    {Localisation::getTranslation('task_view_assign_label')}<br />
                    <input type="text" name="userIdOrEmail" placeholder="{Localisation::getTranslation('task_view_assign_placeholder')}"><br />
                    {if !empty($list_qualified_translators)}
                        <select name="assignUserSelect" id="assignUserSelect" style="width: 500px;">
                            <option value="">...</option>
                            {foreach $list_qualified_translators as $list_qualified_translator}
                                <option value="{$list_qualified_translator['user_id']}">{TemplateHelper::uiCleanseHTML($list_qualified_translator['name'])}</option>
                            {/foreach}
                        </select><br />
                    {/if}
		                <a class="btn btn-primary" onclick="$('#assignTaskToUserForm').submit();">
		                <i class="icon-user icon-white"></i>&nbsp;{Localisation::getTranslation('task_view_assign_button')}
		                </a>
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
		            </form> 
              </td>
              <td>
                <form id="removeUserFromDenyListForm" method="post" action="{urlFor name="task-view" options="task_id.$task_id"}" onsubmit="return confirm('{Localisation::getTranslation("task_view_assign_confirmation")}');">
                    Remove a user from deny list for this task:<br />
                    <input type="text" name="userIdOrEmailDenyList" placeholder="{Localisation::getTranslation('task_view_assign_placeholder')}"><br />
                    <a class="btn btn-primary" onclick="$('#removeUserFromDenyListForm').submit();">
                        <i class="icon-user icon-white"></i>&nbsp;Remove User from Deny List for this Task
                    </a>
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                </form>
              </td>
            </tr></table>

                <a href="{urlFor name="task-search_translators" options="task_id.$task_id"}" class="btn btn-primary">
                    <i class="icon-user icon-white"></i>&nbsp;Search for Translators
                </a>
            </div>
        {/if}

        <p style="margin-bottom: 40px" />

        {if ($roles & ($SITE_ADMIN | $PROJECT_OFFICER | $COMMUNITY_OFFICER | $NGO_ADMIN | $NGO_PROJECT_OFFICER)) && $task->getTaskStatus() > TaskStatusEnum::PENDING_CLAIM}
        <div class="well">
            <strong>{Localisation::getTranslation('task_org_feedback_user_feedback')}</strong><hr/>
            <form id="taskUserFeedback" enctype="application/x-www-form-urlencoded" method="post" action="{urlFor name="task-view" options="task_id.$task_id"}" accept-charset="utf-8">
                <textarea wrap="soft" style="width: 99%" maxlength="4096" rows="10" name="feedback" placeholder="{Localisation::getTranslation('task_org_feedback_1')}"></textarea>
                <p style="margin-bottom:30px;" />

                <span style="float: left; position: relative;">
                    <button type="submit" value="1" name="revokeTask" class="btn btn-inverse">
                        <i class="icon-remove icon-white"></i> {Localisation::getTranslation('task_org_feedback_2')}
                    </button>
                </span>
                <span style="float: right; position: relative;">
                    <button type="submit" value="Submit" name="submit" class="btn btn-success">
                        <i class="icon-upload icon-white"></i> {Localisation::getTranslation('common_submit_feedback')}
                    </button>
                    <button type="reset" value="Reset" name="reset" class="btn btn-primary">
                        <i class="icon-repeat icon-white"></i> {Localisation::getTranslation('common_reset')}
                    </button>
                </span>
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>
        </div>
        {/if}

        {if ($roles & ($SITE_ADMIN | $PROJECT_OFFICER | $COMMUNITY_OFFICER | $NGO_ADMIN | $NGO_PROJECT_OFFICER)) && $task->getTaskStatus() == TaskStatusEnum::COMPLETE && !TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}
            {if !empty($memsource_task)}
                <p>{Localisation::getTranslation('org_task_review_0')}</p>
                <p>
                <a class="btn btn-primary" href="{urlFor name="download-task-latest-version" options="task_id.$task_id"}">
                    <i class="icon-download icon-white"></i> {Localisation::getTranslation('org_task_review_download_output_file')}
                </a>
                </p>
            {/if}

            <h2 class="page-header">
                {Localisation::getTranslation('org_task_review_review_this_file')}
                <small>{Localisation::getTranslation('org_task_review_1')}</small>
            </h2>

            <p>{Localisation::getTranslation('org_task_complete_provide_or_view_review')}</p>
            <p>
                <a class="btn btn-primary" href="{urlFor name="org-task-review" options="org_id.$org_id|task_id.$task_id"}">
                    <i class="icon-list-alt icon-white"></i>{Localisation::getTranslation('org_task_complete_provide_a_review')}
                </a>
                <a class="btn btn-primary" href="{urlFor name="org-task-reviews" options="org_id.$org_id|task_id.$task_id"}">
                    <i class="icon-list icon-white"></i>{Localisation::getTranslation('org_task_complete_view_reviews')}
                </a>
            </p>
        {/if}

		    <p style="margin-bottom: 40px"/>        
        {if !empty($file_preview_path)}
		    <table width="100%">
		        <thead>
                <th>{Localisation::getTranslation('task_view_source_document_preview')} - {TemplateHelper::uiCleanseHTML($filename)}<hr/></th>
		        </thead>
		        <tbody>
		            <tr>
		                <td align="center"><iframe src="https://docs.google.com/viewer?url={$file_preview_path}&embedded=true" width="800" height="780" style="border: none;"></iframe></td>
		            </tr>
		        </tbody>
		    </table>
        {/if}
	{if ($alsoViewedTasksCount>0)}		    
			</div>
	    </div>
    {/if}
    
   
{include file="footer.tpl"}
