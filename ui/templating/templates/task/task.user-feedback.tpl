{include file="header.tpl"}

{assign var="task_id" value=$task->getId()}

    <h1 class="page-header">   
        {if $task->getTitle() != ''}
            {$task->getTitle()}
        {else}
            Task {$task->getId()}
        {/if}
        <small>
            <strong>
                -
                {assign var="type_id" value=$task->getTaskType()}
                {if $type_id == TaskTypeEnum::SEGMENTATION}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::SEGMENTATION]}">Segmentation Task</span>                                    
                {elseif $type_id == TaskTypeEnum::TRANSLATION}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">Translation Task
                {elseif $type_id == TaskTypeEnum::PROOFREADING}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">Proofreading Task
                {elseif $type_id == TaskTypeEnum::DESEGMENTATION}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::DESEGMENTATION]}">Desegmentation Task
                {/if}
            </strong>
        </small>  
    </h1>

    {include file="handle-flash-messages.tpl"}

    <table class="table table-striped" width="100%">
        <thead>
            <th width="25%">Source</th>
            <th width="25%">Target</th>
            <th>Tags</th>        
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
                    <i>There are no tags associated with this project.</i>                    
                {/if}
                </td>
            </tr>
        </tbody>
    </table>
            
    <div class="well">
        <table width="100%" style="overflow-wrap: break-word; table-layout: fixed;">
            <thead>
            <th width="48%" align="left">Task Comment:<hr/></th>
            <th/>
            <th width="48%" align="left">Project Description:<hr/></th>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <i>
                        {if $task->getComment() != ''}
                            {$task->getComment()}
                        {else}
                           No comment has been added.
                        {/if}
                        </i>
                    </td>
                    <td/>
                    <td>
                        <i>
                        {if $project->getDescription() != ''}
                            {$project->getDescription()}
                        {else}
                            No description has been added.
                        {/if}
                        </i>
                    </td>
                </tr>
            </tbody>
        </table>
    </div> 
    
    
    <table class="table table-striped" width="100%">
        <thead>
            <th>Organisation</th>
            <th>Deadline</th>
            <th>Claimed Date</th> 
            <th>Claimed By</th> 
        </thead>
        <tbody>            
            <tr>
                <td><a href="{urlFor name="org-public-profile" options="org_id.{$org->getId()}"}">{$org->getName()}</a></td>
                <td>{date(Settings::get("ui.date_format"), strtotime($task->getDeadline()))}</td>
                <td/>
                <td>
                    {assign var="user_id" value=$claimant->getId()}
                    <a href="{urlFor name="user-public-profile" options="user_id.$user_id"}">{$claimant->getDisplayName()}</a>
                </td>            
            </tr>
        </tbody>
    </table>
                
    <p style="margin-bottom: 40px"/>  

    <div class="well">
        <strong>Organisation Feedback:</strong><hr/>    
        <form id="taskUserFeedback" enctype="application/x-www-form-urlencoded" method="post" 
                action="{urlFor name="task-user-feedback" options="task_id.{$task->getId()}"}">
            <textarea wrap="soft" style="width: 99%" maxlength="4096" rows="10" name="feedback" placeholder="You can provide direct feedback to the organisation who owns this task here. If you have accidentally claimed this task or are unable to complete it, you can also provide the reasons for this decision in this form."></textarea>                    
            <p style="margin-bottom:30px;"/> 

            <span style="float: left; position: relative;">
                <button type="submit" value="1" name="revokeTask" class="btn btn-inverse">
                    <i class="icon-remove icon-white"></i> Unclaim Task & Submit Feedback
                </button>
            </span>
            <span style="float: right; position: relative;">

                <button type="submit" value="Submit" name="submit" class="btn btn-success">
                    <i class="icon-upload icon-white"></i> Submit Feedback
                </button>        
                <button type="reset" value="Reset" name="reset" class="btn btn-primary">
                    <i class="icon-repeat icon-white"></i> Reset
                </button>
            </span>
        </form>
    </div>  
{include file="footer.tpl"}
