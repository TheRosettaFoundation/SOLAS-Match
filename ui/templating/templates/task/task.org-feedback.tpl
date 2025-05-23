{include file="header.tpl"}

{assign var="task_id" value=$task->getId()}

{include file="handle-flash-messages.tpl"}

    <h1 class="page-header">   
        {if $task->getTitle() != ''}
            {TemplateHelper::uiCleanseHTML($task->getTitle())}
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
            {if $isSiteAdmin}
            — <a href="{urlFor name="task-view" options="task_id.$task_id"}">view</a>
            {/if}
        </small>  
    </h1>

    <table class="table table-striped" width="100%">
        <thead>
            <th width="25%">{Localisation::getTranslation('common_source')}</th>
            <th width="25%">{Localisation::getTranslation('common_target')}</th>
            <th>{Localisation::getTranslation('common_tags')}</th>        
        </thead>
        <tbody>

            <tr>
                <td>{if TaskTypeEnum::$enum_to_UI[$type_id]['source_and_target']}{TemplateHelper::getTaskSourceLanguage($task)}{/if}</td>
                <td>{TemplateHelper::getTaskTargetLanguage($task)}</td>
                <td class="nav nav-list unstyled" style="padding-left: 0px; padding-right: 0px;">
                {if isset($task_tags) && is_array($task_tags)}
                    {foreach $task_tags as $tag}
                        {assign var="tag_label" value=TemplateHelper::uiCleanseHTML($tag->getLabel())}
                        {assign var="tagId" value=$tag->getId()}
                        <a class="tag label" href="{urlFor name="tag-details" options="id.$tagId"}">{$tag_label}</a>
                    {/foreach}
                {else}
                    <i>{Localisation::getTranslation('common_there_are_no_tags_associated_with_this_project')}</i>                    
                {/if}
                </td>
            </tr>
        </tbody>
    </table>
            
    <div class="well">
        <table width="100%" style="overflow-wrap: break-word; table-layout: fixed;">
            <thead>
            <th width="48%" align="left">{Localisation::getTranslation('common_task_comment')}<hr/></th>
            <th/>
            <th width="48%" align="left">{Localisation::getTranslation('common_project_description')}<hr/></th>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <i>
                        {if $task->getComment() != ''}
                            {TemplateHelper::uiCleanseHTML($task->getComment())}
                        {else}
                           {Localisation::getTranslation('common_no_comment_has_been_listed')}
                        {/if}
                        </i>
                    </td>
                    <td></td>
                    <td>
                        {if $project->getDescription() != ''}
                            <div class="ql-editor">{TemplateHelper::clean_project_description($project->getDescription())}</div>
                        {else}
                            {Localisation::getTranslation('common_no_description_has_been_listed')}
                        {/if}
                    </td>
                </tr>
            </tbody>
        </table>
    </div> 
    
    
    <table class="table table-striped" width="100%">
        <thead>
            <th>{Localisation::getTranslation('common_deadline')}</th>
            <th>{Localisation::getTranslation('common_claimed_date')}</th> 
            <th>{Localisation::getTranslation('common_claimed_by')}</th> 
        </thead>
        <tbody>            
            <tr>
                <td>{date(Settings::get("ui.date_format"), strtotime($task->getDeadline()))}</td>
                <td>
                    {date(Settings::get("ui.date_format"), strtotime($taskClaimedDate))}
                </td>
                <td>
                    {if $claimant != NULL}
                    {assign var="user_id" value=$claimant->getId()}
                    <a href="{urlFor name="user-public-profile" options="user_id.$user_id"}">{TemplateHelper::uiCleanseHTML($claimant->getDisplayName())}</a>
                    {else}
                    {Localisation::getTranslation('org_task_review_claimant_unavailable')}
                    {/if}
                </td>            
            </tr>
        </tbody>
    </table>
                
    <p style="margin-bottom: 40px"/>  

    <div class="well">
        <strong>{Localisation::getTranslation('task_org_feedback_user_feedback')}</strong><hr/>
        <form id="taskUserFeedback" enctype="application/x-www-form-urlencoded" method="post" 
                    action="{urlFor name="task-org-feedback" options="task_id.$task_id"}" accept-charset="utf-8">
            <textarea wrap="soft" style="width: 99%" maxlength="4096" rows="10" name="feedback" 
                    placeholder="{Localisation::getTranslation('task_org_feedback_1')}"></textarea>                    
            <p style="margin-bottom:30px;"/> 

            <span style="float: left; position: relative;">
                <button type="submit" value="1" name="revokeTask" class="btn btn-inverse">
                    <i class="icon-remove icon-white"></i> {Localisation::getTranslation('task_org_feedback_2')}
                </button>

                <label class="checkbox clear_brand">
                    <input type="checkbox" name="deny_user" value="1" /> Add user to deny list
                </label>
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
{include file="footer.tpl"}
