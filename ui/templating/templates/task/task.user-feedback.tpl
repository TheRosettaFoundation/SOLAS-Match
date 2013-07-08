{include file="header.tpl"}

{assign var="task_id" value=$task->getId()}

    <h1 class="page-header">   
        {if $task->getTitle() != ''}
            {$task->getTitle()}
        {else}
            {Localisation::getTranslation(Strings::COMMON_TASK)} {$task->getId()}
        {/if}
        <small>
            <strong>
                -
                {assign var="type_id" value=$task->getTaskType()}
                {if $type_id == TaskTypeEnum::SEGMENTATION}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::SEGMENTATION]}">{Localisation::getTranslation(Strings::COMMON_SEGMENTATION_TASK)}</span>
                {elseif $type_id == TaskTypeEnum::TRANSLATION}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">{Localisation::getTranslation(Strings::COMMON_TRANSLATION_TASK)}</span>
                {elseif $type_id == TaskTypeEnum::PROOFREADING}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">{Localisation::getTranslation(Strings::COMMON_PROOFREADING_TASK)}</span>
                {elseif $type_id == TaskTypeEnum::DESEGMENTATION}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::DESEGMENTATION]}">{Localisation::getTranslation(Strings::COMMON_DESEGMENTATION_TASK)}</span>
                {/if}
            </strong>
        </small>  
    </h1>

    {include file="handle-flash-messages.tpl"}

    <table class="table table-striped" width="100%">
        <thead>
            <th width="25%">{Localisation::getTranslation(Strings::COMMON_SOURCE)}</th>
            <th width="25%">{Localisation::getTranslation(Strings::COMMON_TARGET)}</th>
            <th>{Localisation::getTranslation(Strings::COMMON_TAGS)}</th>        
        </thead>
        <tbody>
            <tr>
                <td>{TemplateHelper::getTaskSourceLanguage($task)}</td>
                <td>{TemplateHelper::getTaskTargetLanguage($task)}</td>
                <td class="nav nav-list unstyled" style="padding-left: 0px; padding-right: 0px;">
                {if isset($task_tags) && is_array($task_tags)}
                    {foreach $task_tags as $tag}
                        {assign var="tag_label" value=$tag->getLabel()}
                        {assign var="tagId" value=$tag->getId()}
                        <a class="tag label" href="{urlFor name="tag-details" options="id.$tagId"}">{$tag_label}</a>
                    {/foreach}
                {else}
                    <i>{Localisation::getTranslation(Strings::COMMON_THERE_ARE_NO_TAGS_ASSOCIATED_WITH_THIS_PROJECT)}.</i>
                {/if}
                </td>
            </tr>
        </tbody>
    </table>
            
    <div class="well">
        <table width="100%" style="overflow-wrap: break-word; table-layout: fixed;">
            <thead>
            <th width="48%" align="left">{Localisation::getTranslation(Strings::COMMON_TASK_COMMENT)}:<hr/></th>
            <th/>
            <th width="48%" align="left">{Localisation::getTranslation(Strings::COMMON_PROJECT_DESCRIPTION)}:<hr/></th>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <i>
                        {if $task->getComment() != ''}
                            {$task->getComment()}
                        {else}
                           {Localisation::getTranslation(Strings::COMMON_NO_COMMENT_HAS_BEEN_LISTED.)}
                        {/if}
                        </i>
                    </td>
                    <td/>
                    <td>
                        <i>
                        {if $project->getDescription() != ''}
                            {$project->getDescription()}
                        {else}
                            {Localisation::getTranslation(Strings::COMMON_NO_DESCRIPTION_HAS_BEEN_LISTED.)}
                        {/if}
                        </i>
                    </td>
                </tr>
            </tbody>
        </table>
    </div> 
    
    
    <table class="table table-striped" width="100%">
        <thead>
            <th>{Localisation::getTranslation(Strings::COMMON_ORGANISATION)}</th>
            <th>{Localisation::getTranslation(Strings::COMMON_DEADLINE)}</th>
            <th>{Localisation::getTranslation(Strings::COMMON_CLAIMED_DATE)}</th> 
            <th>{Localisation::getTranslation(Strings::COMMON_CLAIMED_BY)}</th> 
        </thead>
        <tbody>            
            <tr>
                <td><a href="{urlFor name="org-public-profile" options="org_id.{$org->getId()}"}">{$org->getName()}</a></td>
                <td>{date(Settings::get("ui.date_format"), strtotime($task->getDeadline()))}</td>
                <td>
                    {date(Settings::get("ui.date_format"), strtotime($taskClaimedDate))}
                </td>
                <td>
                    {assign var="user_id" value=$claimant->getId()}
                    <a href="{urlFor name="user-public-profile" options="user_id.$user_id"}">{$claimant->getDisplayName()}</a>
                </td>            
            </tr>
        </tbody>
    </table>
                
    <p style="margin-bottom: 40px"/>  

    <div class="well">
        <strong>{Localisation::getTranslation(Strings::TASK_USER_FEEDBACK_ORGANISATION_FEEDBACK)}</strong><hr/>    
        <form id="taskUserFeedback" enctype="application/x-www-form-urlencoded" method="post" 
                action="{urlFor name="task-user-feedback" options="task_id.{$task->getId()}"}">
            <textarea wrap="soft" style="width: 99%" maxlength="4096" rows="10" name="feedback" placeholder="{Localisation::getTranslation(Strings::TASK_USER_FEEDBACK_0)} {Localisation::getTranslation(Strings::TASK_USER_FEEDBACK_1)}."></textarea>
            <p style="margin-bottom:30px;"/> 

            <span style="float: left; position: relative;">
                <button type="submit" value="1" name="revokeTask" class="btn btn-inverse">
                    <i class="icon-remove icon-white"></i> {Localisation::getTranslation(Strings::TASK_USER_FEEDBACK_2)}
                </button>
            </span>
            <span style="float: right; position: relative;">

                <button type="submit" value="Submit" name="submit" class="btn btn-success">
                    <i class="icon-upload icon-white"></i> {Localisation::getTranslation(Strings::COMMON_SUBMIT_FEEDBACK)}
                </button>        
                <button type="reset" value="Reset" name="reset" class="btn btn-primary">
                    <i class="icon-repeat icon-white"></i> {Localisation::getTranslation(Strings::COMMON_RESET)}
                </button>
            </span>
        </form>
    </div>  
{include file="footer.tpl"}
