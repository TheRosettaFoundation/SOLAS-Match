{* Must have an object $task assigned by parent *}
<div class="task">
    {assign var='task_id' value=$task->getId()}
        <h2><a href="{urlFor name="task" options="task_id.$task_id"}">{$task->getTitle()}</a></h2>
        <p>
        	{if $task->getSourceLangId()}
        		From <b>{TemplateHelper::languageNameFromId($task->getSourceLangId())}</b>
        	{/if}
        	{if $task->getTargetLangId()}
        		To <b>{TemplateHelper::languageNameFromId($task->getTargetLangId())}</b>
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
    
        {if $task->getStatus()}
            <p><span class="label label-info">{$task->getStatus()}</span></p>
        {/if}
    
	
	<p class="task_details">
		Added {TemplateHelper::timeSinceSqlTime($task->getCreatedTime())} ago
		&middot; By 
        {assign var="org_id" value=$task->getOrgId()}
        <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">
            {TemplateHelper::orgNameFromId($task->getOrgId())}
        </a>
		{if $task->getWordcount()}
			&middot; {$task->getWordcount()|number_format} words
		{/if}
	</p>
        <p style="margin-bottom:40px;"></p>        
</div>
