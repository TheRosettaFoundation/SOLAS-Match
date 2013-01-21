{* Must have an object $task assigned by parent *}
<div class="task">
    {assign var='task_id' value=$task->getId()}
        <h2><a href="{urlFor name="task" options="task_id.$task_id"}">{$task->getTitle()}</a></h2>
        <p>
        	{if $task->getSourceLanguageId()}
        		From <b>{TemplateHelper::languageNameFromId($task->getSourceLanguageId())}</b>
        	{/if}
        	{if $task->getTargetLanguageId()}
        		To <b>{TemplateHelper::languageNameFromId($task->getTargetLanguageId())}</b>
        	{/if}
                 <p>
		    {foreach from=$task->getTags() item=tag}
	    		<a href="{urlFor name="tag-details" options="label.$tag"}" class="label"><span class="label">{$tag}</span></a>
     		{/foreach}
                </p>
    	</p>
        <p>
            Due by {date("D, dS F Y, H:i:s", strtotime($task->getDeadline()))}
        </p>
    
        {if $task->getTaskStatus()}
            <p><span class="label label-info">{$task->getTaskStatus()}</span></p>
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
