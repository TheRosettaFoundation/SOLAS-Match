{include file="header.tpl"}

<h1 class="page-header">
    {if $task->getTitle() != ''}
        {$task->getTitle()}
    {else}
        Task {$task->getId()}
    {/if}
    <small>Task Details</small>
    {assign var="task_id" value=$task->getId()}
    <a href="{urlFor name="task-alter" options="task_id.$task_id"}" class='pull-right btn btn-primary'>
        <i class="icon-wrench icon-white"></i> Edit Details
    </a>
</h1>

{if isset($flash['success'])}
    <p class="alert alert-success">
        {$flash['success']}
    </p>
{/if}

{if isset($flash['error'])}
    <p class="alert alert-error">
        <b>Warning!</b> {$flash['error']}
    </p>
{/if}

{if isset($org)}
    <h3>Organisation</h3>
    <p>
        {assign var="org_id" value=$org->getId()}
        <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">
            {if $org->getName() != ''}
                {$org->getName()}
            {else}
                Organisation Public Profile
            {/if}
        </a>
    </p>
{/if}

{if $task->getImpact() != ''}
    <h3>Impact</h3>
    <p>{$task->getImpact()}</p>
{/if}

{if $task->getReferencePage() != ''}
    <h3>Context Reference</h3>
    <p>
        <a target="_blank" href="{$task->getReferencePage()}">{$task->getReferencePage()}</a>
    </p>
{/if}

{assign var="task_tags" value=$task->getTags()}
{if isset($task_tags)}
    <h3>Task Tags</h3>
    <ul class="nav nav-list unstyled">
        {foreach $task_tags as $tag}
            <li>
                <div class="tag">
                    <a class="label" href="{urlFor name="tag-details" options="label.$tag"}">{$tag}</a>
                </div>
            </li>
        {/foreach}
    </ul>
{/if}

<h3>Source Language</h3>
<p>{TemplateHelper::getTaskSourceLanguage($task)}</p>

<h3>Target Language</h3>
<p>{TemplateHelper::getTaskTargetLanguage($task)}</p>

<h3>Word Count</h3>
<p>{$task->getWordCount()}</p>

<h3>Deadline</h3>
<p>{$task->getDeadline()}</p>

{if isset($user)}
    <hr />

    <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}" class="well">
        {if isset($registered) && $registered == true}
            <p>
                <input type="hidden" name="notify" value="false" />
                <input type="submit" class="btn btn-primary" value="    Ignore Task" />
                You are currently receiving notifications about this task.
                <i class="icon-inbox icon-white" style="position:relative; right:430px; top:2px;"></i> 
            </p>
        {else}
            <p>
                <input type="hidden" name="notify" value="true" />
                <input type="submit" class="btn btn-primary" value="    Track Task" />
                You are not currently receiving notifications about this task.
                <i class="icon-envelope icon-white" style="position:relative; right:446px; top:2px;"></i> 
            </p>
        {/if}
    </form>
{else}
    <p class="alert alert-info">
        Please log in to register for notifications for this task.
    </p>
{/if}

{include file="footer.tpl"}
