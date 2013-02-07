{* Must have an object $task assigned by parent *}
<div class="task">
    {assign var='task_id' value=$task->getId()}
    {assign var="type_id" value=$task->getTaskType()}    
                         
            {if $type_id == TaskTypeEnum::CHUNKING}
                <h2>
                    {if isset($active_tasks)}
                        <a href="{urlFor name="task-chunking" options="task_id.$task_id"}">{$task->getTitle()}</a>
                    {else}
                        <a href="{urlFor name="task-view" options="task_id.$task_id"}">{$task->getTitle()}</a>
                    {/if}
                </h2>
                <p>Type: 
                <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::CHUNKING]}">Chunking</span>  

            {elseif $type_id == TaskTypeEnum::TRANSLATION}

                <h2>
                    {if isset($active_tasks)}
                        <a href="{urlFor name="task" options="task_id.$task_id"}">{$task->getTitle()}</a>
                    {else}
                        <a href="{urlFor name="task-view" options="task_id.$task_id"}">{$task->getTitle()}</a>
                    {/if}
                </h2>
                <p>Type: 
                <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">Translation</span>
            {elseif $type_id == TaskTypeEnum::PROOFREADING}
                <h2>
                    {if isset($active_tasks)}
                        <a href="{urlFor name="task" options="task_id.$task_id"}">{$task->getTitle()}</a>
                    {else}
                        <a href="{urlFor name="task-view" options="task_id.$task_id"}">{$task->getTitle()}</a>
                    {/if}
                </h2>
                <p>Type: 
                <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">Proofreading</span>
            {elseif $type_id == TaskTypeEnum::POSTEDITING}
                <h2>
                    {if isset($active_tasks)}
                        <a href="{urlFor name="task" options="task_id.$task_id"}">{$task->getTitle()}</a>
                    {else}
                        <a href="{urlFor name="task-view" options="task_id.$task_id"}">{$task->getTitle()}</a>
                    {/if}
                </h2>
                <p>Type: 
                <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::POSTEDITING]}">Postediting</span>
            {/if}                
        </p>

        <p>
        	{if $task->getSourceLanguageCode()}
        		From: <b>{TemplateHelper::languageNameFromCode($task->getSourceLanguageCode())}</b>
        	{/if}
        	{if $task->getTargetLanguageCode()}
        		To: <b>{TemplateHelper::languageNameFromCode($task->getTargetLanguageCode())}</b>
        	{/if}
    	</p>
        <p>
            Due by: <b>{date(Settings::get("ui.date_format"), strtotime($task->getDeadline()))}</b>
        </p>       

        {assign var="taskTags" value=$task->getTagList()}
        {if !empty($taskTags)}
            <p>
                Tags:
                {foreach from=$task->getTagList() item=tag}
                    {assign var="label" value=$tag->getLabel()}
                    <a href="{urlFor name="tag-details" options="label.$label"}" class="label"><span class="label">{$label}</span></a>
                {/foreach}
            </p>
        {/if}
        
	<p class="task_details">
		Added <b>{TemplateHelper::timeSinceSqlTime($task->getCreatedTime())}</b> ago
		&middot;
        {assign var="project_id" value=$task->getProjectId()}
        <a href="{urlFor name="project-view" options="project_id.$project_id"}">
            Project Page
        </a>
		{if $task->getWordCount()}
			&middot; {$task->getWordCount()|number_format} words
		{/if}
	</p>
        <p style="margin-bottom:40px;"></p>        
</div>
