{include file="header.tpl"}
<!-- Editor Hint: ¿áéíóú -->

    <h1 class="page-header">
        <span style="height: auto; width: 750px; overflow-wrap: break-word; display: inline-block;">
            {TemplateHelper::uiCleanseHTMLNewlineAndTabs($project->getTitle())}
            <small>{Localisation::getTranslation('project_view_overview_of_project_details')}</small>
        </span>
        {assign var="project_id" value=$project->getId()}
        <div class="pull-right top_btn">
            <form id="copyChunksProjectForm" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
                {if $isSiteAdmin && !empty($matecat_analyze_url)}
                    <input type="hidden" name="copyChunks" value="1" />
                    <a class="btn btn-success" onclick="$('#copyChunksProjectForm').submit();" >
                        <i class="icon-upload icon-white"></i> Sync Phrase TMS
                    </a>
                    <a href="{$matecat_analyze_url}" class="btn btn-primary" target="_blank">
                        <i class="icon-th-list icon-white"></i> {if !empty($memsource_project)}Phrase TMS Project{else}Kató TM analysis{/if}
                    </a>
                {/if}
                {if (!$isOrgMember)}
                    {if false}
                        {if ($userSubscribedToOrganisation)}
                            <input type="hidden" name="trackOrganisation" value="0" />
                                <a class="btn btn-small btn-inverse pull-right" onclick="$('#trackedOrganisationForm').submit();" >
                                    <i class="icon-remove-circle icon-white"></i>{Localisation::getTranslation('org_public_profile_untrack_organisation')}
                                </a>
                        {else}
                            <input type="hidden" name="trackOrganisation" value="1" />
                                <a class="btn btn-small pull-right" onclick="$('#trackedOrganisationForm').submit();" >
                                    <i class="icon-envelope icon-black"></i>{Localisation::getTranslation('org_public_profile_track_organisation')}
                                </a>
                        {/if}
                    {/if}
                {/if}
                {if ($isOrgMember || $isAdmin)}
                    <a href="{urlFor name="project-alter" options="project_id.$project_id"}" class='btn btn-primary fixMargin'>
                        <i class="icon-wrench icon-white"></i> {Localisation::getTranslation('common_edit_project')}
                    </a> 
                {/if}
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>
        </div>
    </h1>

{if isset($flash['success'])}
    <p class="alert alert-success">
        {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}
    </p>
{/if}

{if isset($flash['error'])}
    <p class="alert alert-error">
        {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
    </p>
{/if}

    <table class="table table-striped" style="overflow-wrap: break-word; table-layout: fixed;">
        <thead>            
            <th style="text-align: left;"><strong>{Localisation::getTranslation('common_organisation')}</strong></th>
            <th>{Localisation::getTranslation('common_source_language')}</th>
            <th>{Localisation::getTranslation('common_reference')}</th>
            <th>{Localisation::getTranslation('common_word_count')}</th>
            <th>{Localisation::getTranslation('common_created')}</th>
            <th>{Localisation::getTranslation('project_view_project_deadline')}</th>
            {if isset($userSubscribedToProject)}
                <th>{Localisation::getTranslation('common_tracking')}</th>
            {/if}

        </thead>
        <tbody>
            <tr style="overflow-wrap: break-word;">
                <td style="text-align: left; overflow-wrap: break-word;">
                    {if isset($org)}
                        {assign var="org_id" value=$org->getId()}
                        <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">{$org->getName()|escape:'html':'UTF-8'}</a>
                    {/if}
                </td>
                <td>
                    {TemplateHelper::getTaskSourceLanguage($project)}
                </td>
                <td>
                    {if $project->getReference() != ''}
                        <a target="_blank" href="{TemplateHelper::uiCleanseHTML($project->getReference())}">{TemplateHelper::uiCleanseHTML($project->getReference())}</a>
                    {else}
                        -
                    {/if}
                </td>
                <td>
                    <span class="hidden">
                        <div id="siteLocationURL">{Settings::get("site.location")}</div>
                        <div id="project_id_for_updated_wordcount">{$project_id}</div>
                    </span>
                    <div id="put_updated_wordcount_here">{if $project->getWordCount() != '' && $project->getWordCount() > 1}{$project->getWordCount()}{else}-{/if}</div>
                </td>
                <td>
                    <div class="convert_utc_to_local" style="visibility: hidden">{$project->getCreatedTime()}</div><br />{$pm}
                </td>  
                <td>
                    <div class="convert_utc_to_local_deadline" style="visibility: hidden">{$project->getDeadline()}</div>
                </td>
                {if isset($userSubscribedToProject)}
                    <td>

                        <form id="trackedProjectForm" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
                             {if $userSubscribedToProject}
                                <p>
                                    <input type="hidden" name="trackProject" value="0" />
                                    <a class="btn btn-small btn-inverse" onclick="$('#trackedProjectForm').submit();" >
                                        <i class="icon-remove-circle icon-white"></i> {Localisation::getTranslation('project_view_untrack_project')}
                                    </a>
                                </p>
                            {else}
                                <p>
                                    <input type="hidden" name="trackProject" value="1" />
                                    <a class="btn btn-small" onclick="$('#trackedProjectForm').submit();" >
                                        <i class="icon-envelope icon-black"></i> {Localisation::getTranslation('common_track_project')}
                                    </a>
                                </p>
                            {/if}
                            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                        </form>
                    </td>
                {/if}
            </tr>
            <tr>
            </tr> 
        </tbody>
    </table>            
            
    <div class="well">
        <table border="0" width="100%" style="overflow-wrap: break-word; table-layout: fixed;">
            <thead>
            <th align="left" width="48%">{Localisation::getTranslation('common_description')}<hr/></th>
            <th></th>
            <th align="left" width="48%">{Localisation::getTranslation('common_project_image')}<hr/></th>
            </thead>
            <tbody>
                <tr valign="top">
                    <td>
                        <i>
                        {if $project->getDescription() != ''}
                            {TemplateHelper::uiCleanseHTMLNewlineAndTabs($project->getDescription())}
                        {else}
                            {Localisation::getTranslation('common_no_description_has_been_listed')}
                        {/if}  
                        </i>
                    </td>
                    <td></td>
                    <td style = "text-align:center;">
                    	{if $project->getImageUploaded()}
	                        {if $isSiteAdmin}
	                        	<img class="project-image" src="{urlFor name="download-project-image" options="project_id.$project_id"}?{$imgCacheToken}"/>
		                        {if !$project->getImageApproved()}
		                        	<form id="projectImageApproveForm" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
		                       			<input type="hidden" name="imageApprove" value="0" />
		                        		<a class="image-approve-btn btn btn-success" onclick="$('#projectImageApproveForm').submit();">
		            					<i class="icon-check icon-white"></i> {Localisation::getTranslation('project_view_image_approve')}</a>
                            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
		            				</form>
		            			{else}   
		            				 <form id="projectImageApproveForm" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
		            				 	<input type="hidden" name="imageApprove" value="1" />
		                        		<a class="image-approve-btn btn btn-inverse" onclick="$('#projectImageApproveForm').submit();"">
		            					<i class="icon-check icon-white"></i> {Localisation::getTranslation('project_view_image_disapprove')}</a>
                             {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
		            				 </form>
		                        {/if}
		                    {else}
		                    	{if $project->getImageApproved()}
		                    		<img class="project-image" src="{urlFor name="download-project-image" options="project_id.$project_id"}?{$imgCacheToken}"/>
		                    	{else}
			                    	{Localisation::getTranslation('common_project_image_not_approved')}
		                    	{/if}
		                    {/if}
		                {else}
		                	{Localisation::getTranslation('common_project_image_not_uploaded')}
                    	{/if}
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="padding-bottom: 40px"></td>
                </tr>
                
                 <tr valign="top">
                    <td colspan="3">
                        <strong>{Localisation::getTranslation('common_impact')}</strong><hr/>
                    </td>
                </tr>
                <tr>                
                    <td class="nav nav-list unstyled" style="padding-left: 0px; padding-right: 0px;" colspan="2">
                        <i>
	                        {if $project->getImpact() != ''}
                              {TemplateHelper::uiCleanseHTMLNewlineAndTabs($project->getImpact())}
	                        {else}
	                            {Localisation::getTranslation('No impact has been listed')}
	                        {/if}  
                        </i> 
                    </td>                
                </tr>
                <tr>
                    <td colspan="3" style="padding-bottom: 40px"></td>
                </tr>
                <tr valign="top">
                    <td colspan="3">
                        <strong>{Localisation::getTranslation('common_tags')}</strong><hr/>
                    </td>
                </tr>
                <tr>                
                    <td class="nav nav-list unstyled" style="padding-left: 0px; padding-right: 0px;" colspan="2">
                    {if isset($project_tags) && is_array($project_tags)}
                        {foreach $project_tags as $ptag}
                            {assign var="tag_label" value=TemplateHelper::uiCleanseHTML($ptag->getLabel())}
                            {assign var="tagId" value=$ptag->getId()}
                            <a class="tag label" href="{urlFor name="tag-details" options="id.$tagId"}">{$tag_label}</a>
                        {/foreach}
                    {else}
                        <i>{Localisation::getTranslation('common_there_are_no_tags_associated_with_this_project')}</i>                    
                    {/if}
                    </td>                
                </tr>
                {if $project_id > Settings::get("discourse.pre_discourse") && !preg_match('/^Test.{4}$/', $project->getTitle())}
                <tr>
                    <td colspan="3" style="padding-bottom: 40px"></td>
                </tr>
                <tr valign="top">
                    <td colspan="3">
                        <strong>{Localisation::getTranslation('common_discuss_on_community')}</strong><hr/>
                    </td>
                </tr>
                <tr>
                    <td class="nav nav-list unstyled" style="padding-left: 0px; padding-right: 0px;" colspan="2">
                        <a href="https://community.translatorswb.org/t/{$discourse_slug}" target="_blank">https://community.translatorswb.org/t/{$discourse_slug}</a>
                    </td>
                </tr>
                {/if}
            </tbody>
        </table>
    </div>            
                
    <p style="margin-bottom:40px;"/>

{if isset($user) && ($isOrgMember || $isAdmin)}
    <hr />    
    <h1 class="page-header" style="margin-bottom: 60px">
        {Localisation::getTranslation('project_view_tasks')}
        <small>{Localisation::getTranslation('project_view_0')}</small>

        {if !empty($memsource_project)}
        <span class="" style="margin-left:480px;">
            <select name="task_options" id="task_options">
                <option value="">-- Choose --</option>
                <option value="all_tasks">Select all Tasks</option>
                <option value="all_translation_tasks">Select all Translation Tasks</option>
                <option value="all_revision_tasks">Select all Revision Tasks</option>
                <option value="all_approval_tasks" id="all_approval_tasks">Select all Approval Tasks</option>
                <option value="delesect_all">Deselect all</option>
            </select>
        </span>

        <div class="pull-right">
        <div class="dropdown"  style="margin-top: -65px;">
            <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="/page.html">
                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
            </a>
            <ul id="menu3" class="dropdown-menu" role="menu" aria-labelledby="drop5">

        {if $isAdmin || $isOrgMember}
            <form id="publish_selected_tasks" class=" btn btn-small" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}" >
                <a class="" onclick="$('#publish_selected_tasks').submit();" style="color:#000000;margin-right:65px;">
                    <i class="icon-check icon-black" style="margin-left:-2px;"></i> Publish Selected Tasks
                </a>
                <input type="hidden" name="publish_selected_tasks" value="1" />
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>

            <form id="unpublish_selected_tasks" class=" btn btn-small" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}" >
                <a class="" onclick="$('#unpublish_selected_tasks').submit();"   style="color:#000000;margin-right:52px;">
                    <i class="icon-remove-circle icon-black" style="margin-left:-2px;"></i> Unpublish Selected Tasks
                </a>
                <input type="hidden" name="unpublish_selected_tasks" value="" />
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>
        {/if}

        {if $isSiteAdmin}
            <form id="tasks_as_paid" class=" btn btn-small" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}" >
                <a class="" onclick="$('#tasks_as_paid').submit();" style="color:#000000;margin-right:42px;">
                    <i class="fa fa-usd" style="font-size: 15px !important;padding:0 !important;width:5px !important;" aria-hidden="true"></i> Mark Selected Tasks as Paid
                </a>
                <input type="hidden" name="tasks_as_paid" value="" />
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>

            <form id="tasks_as_unpaid" class=" btn btn-small" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}" >
                <a class="" onclick="$('#tasks_as_unpaid').submit();" style="color:#000000;margin-right:22px;">
                    <i class="fa fa-strikethrough" style="font-size: 15px !important;padding:0 !important;width:12px !important;margin-left:-2px;" aria-hidden="true"></i> Mark Selected Tasks as Unpaid
                </a>
                <input type="hidden" name="all_as_paid1" value="1" />
                <input type="hidden" name="tasks_as_unpaid" value="" />
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>
        {/if}

            <form id="status_as_unclaimed" class=" btn btn-small" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}" >
                <a class="" onclick="$('#status_as_unclaimed').submit();" >
                    <i class="fa fa-unlock" style="font-size: 15px !important;padding:0 !important;width:12px !important;" aria-hidden="true"></i> Set Status of Selected to Unclaimed
                </a>
                <input type="hidden" name="status_as_unclaimed" value="" />
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>

            <form id="status_as_waiting" class=" btn btn-small" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}" >
                <a class="" onclick="$('#status_as_waiting').submit();" style="color:#000000;margin-right:15px;">
                    <i class="fa fa-pause" style="font-size: 15px !important;padding:0 !important;width:12px !important;" aria-hidden="true"></i> Set Status of Selected to Waiting
                </a>
                <input type="hidden" name="status_as_waiting" value="" />
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>

            </ul>
        </div>
        </div>
        {/if}
    </h1> 
            
    {if isset($flash['taskSuccess'])}
        <div class="alert alert-success">
            {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['taskSuccess'])}
        </div>
    {else if isset($flash['taskError'])}
        <div class="alert alert-error">
            {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['taskError'])}
        </div>
    {/if}        

    <div>
        <div>
            {if isset($projectTasks) && count($projectTasks) > 0}
                {foreach from=$taskLanguageMap key=languageCountry item=tasks}
                <br/><br/>
                <div style="background-color:#fef9f2;padding:3px;">
                    <div>
                    <span style="display: inline-block; overflow-wrap: break-word; font-weight: bold; font-size: large; max-width: 70%" class="language_name">
                        {TemplateHelper::getLanguageAndCountryFromCode($languageCountry)}
                    </span>
                    <span>
                        <select name="language_options[]" id="language_options" id="language_options" data-select-name="{TemplateHelper::getLanguageAndCountryFromCode($languageCountry)|strstr:' ':true}">
                            <option value="">-- Choose --</option>
                            <option value="all_tasks_{TemplateHelper::getLanguageAndCountryFromCode($languageCountry)|strstr:' ':true}">Select all Tasks</option>
                            <option value="all_translation_tasks_{TemplateHelper::getLanguageAndCountryFromCode($languageCountry)|strstr:' ':true}">Select all Translation Tasks</option>
                            <option value="all_revision_tasks_{TemplateHelper::getLanguageAndCountryFromCode($languageCountry)|strstr:' ':true}">Select all Revision Tasks</option>
                            <option value="all_approval_tasks_{TemplateHelper::getLanguageAndCountryFromCode($languageCountry)|strstr:' ':true}" id="all_approval_tasks_lang">Select all Approval Tasks</option>
                            <option value="delesect_all_{TemplateHelper::getLanguageAndCountryFromCode($languageCountry)|strstr:' ':true}">Deselect all</option>
                        </select>
                    </span>
                    </div>                
                    <hr />  
 
                    <table class="table table-striped" style="overflow-wrap: break-word; margin-bottom: 60px">
                        <thead>
                            <tr>
                                
                                 <th><input type="checkbox" name="select_all_tasks" data-lang="{TemplateHelper::getLanguageAndCountryFromCode($languageCountry)|strstr:' ':true}" /></th>
                                 <th>{Localisation::getTranslation('common_title')}</th>
                                 <th>{Localisation::getTranslation('common_status')}</th>       
                                 <th>{Localisation::getTranslation('common_type')}</th> 
                                {if $isSiteAdmin}
                                 <th>Paid?</th>
                                {/if}
                                 <th>{Localisation::getTranslation('common_task_deadline')}</th>                  
                                 <th>{Localisation::getTranslation('common_publish')}</th>
                                 <th>{Localisation::getTranslation('common_tracking')}</th>
                                 <th>{Localisation::getTranslation('common_edit')}</th>
                                 <th>{Localisation::getTranslation('project_view_archive_delete')}</th>
                            </tr>
                        </thead>
                        <tbody>

                            {foreach from=$tasks item=task}
                                {assign var="task_id" value=$task->getId()}
                                <tr style="overflow-wrap: break-word;">
                                <td> <input type="checkbox" name="select_task" value="{$task->getId()}" data-task-type="{$task->getTaskType()}" data-lang="{TemplateHelper::getLanguageAndCountryFromCode($languageCountry)|strstr:' ':true}" /> </td>
                                    <td width="24%">
                                        <a href="{urlFor name="task-view" options="task_id.$task_id"}">
                                            {TemplateHelper::uiCleanseHTMLNewlineAndTabs($task->getTitle())}
                                        </a>
                                        <br/>
                                    </td>
                                    <td>
                                        {assign var="status_id" value=$task->getTaskStatus()}
                                        {if $status_id == TaskStatusEnum::WAITING_FOR_PREREQUISITES}
                                            {Localisation::getTranslation('common_waiting')}
                                        {elseif $status_id == TaskStatusEnum::PENDING_CLAIM}
                                            {Localisation::getTranslation('common_unclaimed')}
                                        {elseif $status_id == TaskStatusEnum::IN_PROGRESS}
                                            <a href="{urlFor name="task-org-feedback" options="task_id.$task_id"}">
                                                {Localisation::getTranslation('common_in_progress')}
                                            </a><br />
                                            {$user_id = $users_who_claimed[$task_id]['user_id']}
                                            <i class="icon-user icon-black"></i> <a style="color:#000000;" href="{urlFor name="user-public-profile" options="user_id.$user_id"}" data-toggle="tooltip" data-placement="right" data-original-title="Task claimed by {$users_who_claimed[$task_id]['display_name']}">{TemplateHelper::uiCleanseHTML($users_who_claimed[$task_id]['display_name'])}</a>
                                        {elseif $status_id == TaskStatusEnum::CLAIMED}
                                            <a href="{urlFor name="task-org-feedback" options="task_id.$task_id"}">
                                                Claimed
                                            </a><br />
                                            {if !empty($users_who_claimed[$task_id])}
                                                {$user_id = $users_who_claimed[$task_id]['user_id']}
                                             <i class="icon-user icon-black"></i>   <a style="color:#000000;" href="{urlFor name="user-public-profile" options="user_id.$user_id"}" data-toggle="tooltip" data-placement="right" data-original-title="Task claimed by {$users_who_claimed[$task_id]['display_name']}">{TemplateHelper::uiCleanseHTML($users_who_claimed[$task_id]['display_name'])}</a>
                                            {/if}
                                        {elseif $status_id == TaskStatusEnum::COMPLETE}
                                            {assign var="org_id" value=$project->getOrganisationId()}
                                            <a href="{urlFor name="org-task-complete" options="task_id.$task_id|org_id.$org_id"}">
                                                {Localisation::getTranslation('common_complete')}
                                            </a>
                                            <br />
                                            <a class="btn btn-primary" target="_blank" href="{urlFor name="download-task-latest-version" options="task_id.$task_id"}" data-toggle="tooltip" data-placement="bottom" data-original-title="Download Output File">
                                                <i class="icon-download-alt icon-white"></i>
                                            </a>
                                            <br />
                                            {$user_id = $users_who_claimed[$task_id]['user_id']}
                                            <i class="icon-user icon-black"></i>   <a style="color:#000000;" href="{urlFor name="user-public-profile" options="user_id.$user_id"}" data-toggle="tooltip" data-placement="right" data-original-title="Task claimed by {$users_who_claimed[$task_id]['display_name']}">{TemplateHelper::uiCleanseHTML($users_who_claimed[$task_id]['display_name'])}</a>
                                        {/if}
                                    </td>
                                    <td>
                                        <strong>
                                            <small>                                  
                                                {assign var="type_id" value=$task->getTaskType()}
                                                {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                                                    {if $type_id == $task_type}
                                                        <span style="color: {$ui['colour']}">{$ui['type_text']}</span>
                                                    {/if}
                                                {/foreach}
                                            </small>
                                        </strong>
                                    </td>
                                    {if $isSiteAdmin}
                                    <td>                                    
                                     {if $get_paid_for_project[$task_id] == 1}
                                         <span>Paid</span>
                                     {else}
                                         <span>-</span>
                                     {/if}
                                    </td>
                                    {/if}
                                    <td>
                                        <div class="convert_utc_to_local_deadline" style="visibility: hidden">{$task->getDeadline()}</div>
                                    </td>
                                    <td>
                                        <form id="publishedForm{$task_id}" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}" style="text-align: center">
                                            <input type="hidden" name="task_id" value="{$task_id}" />
                                            {if $task->getPublished() == 1}
                                                <a class="btn btn-small btn-inverse" onclick="$('#publishedForm{$task_id}').submit();" data-toggle="tooltip" data-placement="bottom" title="{Localisation::getTranslation('common_unpublish')}">
                                                    <i class="icon-check icon-white"></i>
                                                </a>
                                                <input type="hidden" name="publishedTask" value="0" />
                                            {else}
                                                <input type="hidden" name="publishedTask" value="1" />
                                                <a class="btn btn-small" onclick="$('#publishedForm{$task_id}').submit();" data-toggle="tooltip" data-placement="bottom" title="{Localisation::getTranslation('common_publish')}" >
                                                    <i class="icon-remove-circle icon-black"></i>
                                                </a>
                                            {/if}
                                            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                                        </form>
                                    </td>
                                    <td>
                                        <form id="trackedForm{$task_id}" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
                                            <input type="hidden" name="task_id" value="{$task_id}" />
                                            {if $taskMetaData[$task_id]['tracking']}
                                                <input type="hidden" name="trackTask" value="0" />
                                                <a class="btn btn-small btn-inverse" onclick="$('#trackedForm{$task_id}').submit();" data-toggle="tooltip" data-placement="bottom" title="{Localisation::getTranslation('common_untrack_task')}">
                                                    <i class="icon-inbox icon-white"></i>
                                                </a>
                                            {else}
                                                <input type="hidden" name="trackTask" value="1" />
                                                <a class="btn btn-small" onclick="$('#trackedForm{$task_id}').submit();" data-toggle="tooltip" data-placement="bottom" title="{Localisation::getTranslation('common_track_task')}" >
                                                    <i class="icon-envelope icon-black"></i>
                                                </a>
                                            {/if}
                                            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                                        </form>
                                    </td>    
                                    <td>
                                        <a href="{urlFor name="task-alter" options="task_id.$task_id"}" class="btn btn-small" data-toggle="tooltip" data-placement="bottom" title="{Localisation::getTranslation('project_view_edit_task')}">
                                            <i class="icon-pencil icon-black"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <form id="archiveDeleteForm{$task_id}" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
                                            <input type="hidden" name="task_id" value="{$task_id}" />
                                            {if $status_id < TaskStatusEnum::IN_PROGRESS}
                                                <input type="hidden" name="deleteTask" value="Delete" />
                                                <a class="btn btn-small btn-inverse" 
                                                    onclick="if (confirm('{Localisation::getTranslation('project_view_1')}')) 
                                                        $('#archiveDeleteForm{$task_id}').submit();" data-toggle="tooltip" data-placement="bottom" title="{Localisation::getTranslation('common_delete')}" >
                                                    <i class="icon-trash icon-white"></i>
                                                </a> 
                                            {elseif $status_id == TaskStatusEnum::IN_PROGRESS || $status_id == TaskStatusEnum::CLAIMED}
                                                <div class="tooltip-wrapper" style="display: inline-block;margin: 5px;" data-toggle="tooltip" data-placement="bottom" title="{Localisation::getTranslation('project_view_2')}">  <button style="pointer-events: none;" class="btn btn-small btn-inverse" disabled >
                                                    <i class="icon-trash icon-white"></i>
                                                 </button> 
                                                </div>
                                            {else}
                                                {if $isSiteAdmin}
                                                <input type="hidden" name="archiveTask" value="Delete" />
                                                <a class="btn btn-small btn-inverse"
                                                    onclick="if (confirm('{Localisation::getTranslation('project_view_3')}'))
                                                        $('#archiveDeleteForm{$task_id}').submit();" data-toggle="tooltip" data-placement="bottom" title="{Localisation::getTranslation('common_archive')}">
                                                    <i class="icon-fire icon-white"></i>
                                                </a>
                                                {/if}
                                            {/if}
                                            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                                        </form>
                                    </td>
                                </tr>                        
                            {/foreach}
                        </tbody>
                    </table>
                </div>
                {/foreach}
            {else}
                <div class="alert alert-warning">
                    <strong>{Localisation::getTranslation('common_what_happens_now')}?</strong> {Localisation::getTranslation('project_view_4')}
                    {Localisation::getTranslation('project_view_5')}
                </div>
            {/if}
        </div>
    </div>
        
{else}
    {if isset($projectTasks)}
    <p class="alert alert-info">
        {Localisation::getTranslation('project_view_6')}
    </p>
    {/if}
{/if}

{if !empty($volunteerTaskLanguageMap)}
    <hr />
    <h1 class="page-header" style="margin-bottom: 60px">
        {Localisation::getTranslation('project_view_tasks')}
        <small>{Localisation::getTranslation('project_view_0')}</small>
    </h1>
                {foreach from=$volunteerTaskLanguageMap key=languageCountry item=tasks}

                    <div style="display: inline-block; overflow-wrap: break-word;
                                    font-weight: bold; font-size: large; max-width: 70%">
                        {TemplateHelper::getLanguageAndCountryFromCode($languageCountry)}
                    </div>
                    <hr />

                    <table class="table table-striped" style="overflow-wrap: break-word; margin-bottom: 60px">
                        <thead>
                            <tr>
                                <th>{Localisation::getTranslation('common_title')}</th>
                                <th>{Localisation::getTranslation('common_status')}</th>
                                <th>{Localisation::getTranslation('common_type')}</th>
                                <th>{Localisation::getTranslation('common_task_deadline')}</th>
                            </tr>
                        </thead>
                        <tbody>

                            {foreach from=$tasks item=task}
                                {assign var="task_id" value=$task['task_id']}
                                <tr style="overflow-wrap: break-word;">
                                    <td width="24%">
                                        <a href="{urlFor name="task-view" options="task_id.$task_id"}">
                                            {TemplateHelper::uiCleanseHTMLNewlineAndTabs($task['title'])}
                                        </a>
                                        <br/>
                                    </td>
                                    <td>
                                        {assign var="status_id" value=$task['status_id']}
                                        {if $status_id == TaskStatusEnum::WAITING_FOR_PREREQUISITES}
                                            {Localisation::getTranslation('common_waiting')}
                                        {elseif $status_id == TaskStatusEnum::PENDING_CLAIM}
                                            {Localisation::getTranslation('common_unclaimed')}
                                        {elseif $status_id == TaskStatusEnum::IN_PROGRESS}
                                            {Localisation::getTranslation('common_in_progress')}
                                        {elseif $status_id == TaskStatusEnum::CLAIMED}
                                            Claimed
                                        {elseif $status_id == TaskStatusEnum::COMPLETE}
                                            {Localisation::getTranslation('common_complete')}
                                        {/if}
                                    </td>
                                    <td>
                                        <strong>
                                            <small>
                                                {assign var="type_id" value=$task['type_id']}
                                                {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                                                    {if $type_id == $task_type}
                                                        <span style="color: {$ui['colour']}">{$ui['type_text']}</span>
                                                    {/if}
                                                {/foreach}
                                            </small>
                                        </strong>
                                    </td>
                                    <td>
                                        <div class="convert_utc_to_local_deadline" style="visibility: hidden">{$task['deadline']}</div>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                {/foreach}
{/if}

{include file="footer_no_end.tpl"}
        <script>
            $("[data-toggle='tooltip']").tooltip(); // Initialize Tooltip
        </script>
    </body>
</html>
