{include file="header.tpl"}
<div class="page-header">
	<h1>Tasks related to "{$tag}" <small>Find tasks tagged with this tag</small>
    {if isset($user_id)}
    {/if}
    </h1>
</div>

<div class="row">
	<div class="span8">
        {if isset($user_id)}
            <form method="post" action="{urlFor name="tag-details" options="label.$tag"}">
            {if isset($subscribed)}
                <button type="submit" class="btn btn-primary" name="remove" title="Remove tag from a list of tags you have subscribed to">Unsubscribe</button>
            {else}
                <button type="submit" class="btn btn-primary" name="save" title="Save the tag to a list of subscribed tags">Subscribe to Tag</button>
            {/if}
        {/if}

		{if isset($tasks)}
			{foreach from=$tasks item=task}
				{include file="task.summary-link.tpl" task=$task}
			{/foreach}
		{else}
			<div class="alert alert-warning">
				<strong>No open tasks</strong> Sorry, there are currently no open tasks for this label.
			</div>
		{/if}
        {if isset($warning)}
            <div class="alert alert-error">{$warning}</div>
        {/if}
	</div>

    <div class="span4">
        {include file="tags.user-tags.inc.tpl"}
    </div>
	<div class="span4">
		{include file="tags.top-list.inc.tpl"}
	</div>
</div>

{include file="footer.tpl"}
