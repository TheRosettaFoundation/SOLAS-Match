{* Must have an object $task assigned by parent *}
<div class="task">
    {assign var='task_id' value=$task->getId()}
        <h2><a href="{urlFor name="task" options="task_id.$task_id"}">{$task->getTitle()}</a></h2>
        <p>
        	{if $task->getSourceLanguageCode()}
        		From <b>{TemplateHelper::languageNameFromCode($task->getSourceLanguageCode())}</b>
        	{/if}
        	{if $task->getTargetLanguageCode()}
        		To <b>{TemplateHelper::languageNameFromCode($task->getTargetLanguageCode())}</b>
        	{/if}
    	</p>
        <p>
            Due by {date("D, dS F Y, H:i:s", strtotime($task->getDeadline()))}
        </p>
        
        {if $task->getTaskType()}
            {assign var="type_id" value=$task->getTaskType()}
            <p>
                <b>Task Type:</b>                  
                    {if $type_id == TaskTypeEnum::CHUNKING}
                        <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::CHUNKING]}">Chunking</span>                                    
                    {elseif $type_id == TaskTypeEnum::TRANSLATION}
                        <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">Translation</span>
                    {elseif $type_id == TaskTypeEnum::PROOFREADING}
                        <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">Proofreading</span>
                    {elseif $type_id == TaskTypeEnum::POSTEDITING}
                        <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::POSTEDITING]}">Postediting</span>
                    {/if}                
            </p>
        {/if}
        {if isset($task)}
            <p>
                {foreach from=$task->getTags() item=tag}
                    <a href="{urlFor name="tag-details" options="label.$tag"}" class="label"><span class="label">{$tag}</span></a>
                {/foreach}
            </p>
        {/if}
        
	<p class="task_details">
		Added {TemplateHelper::timeSinceSqlTime($task->getCreatedTime())} ago
		&middot; By 
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
