{* Must have an object $task assigned by parent *}
<div class="task">
    {assign var='task_id' value=$task->getId()}
	<h2>{$task->getTitle()}</h2>
	<p>
            From <strong>TemplateHelper::getLanguage($task->getSourceLocale())</strong>
            To <strong>TemplateHelper::getLanguage($task->getTargetLocale())</strong>    

            {foreach from=$task->getTags() item=tag}
                <span class="label">{$tag}</span>                        
            {/foreach}
	</p>
	
	<p class="task_details">
            Added {TemplateHelper::timeSinceSqlTime($task->getCreatedTime())} ago
            &middot; By Project
            {if $task->getWordCount()}
                    &middot; {$task->getWordCount()|number_format} words
            {/if}
            <p style="margin-bottom:30px;"/>
	</p>
</div>
