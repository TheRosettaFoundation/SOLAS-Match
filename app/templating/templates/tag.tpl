{include file="header.tpl"}
<div class="page-header">
	<h1>Tasks related to "{$tag}" <small>Find tasks tagged with this tag</small>
    </h1>
</div>
{include file="handle-flash-messages.tpl"}
<div class="row">
	<div class="span8">
        {if isset($user)}
            {if isset($subscribed)}
                <a href="{urlFor name="tag-subscribe" options="label.$tag|subscribe.false"}">
                <button class="btn btn-primary" title="Remove tag from a list of tags you have subscribed to">Unsubscribe</button>
            {else}
                <a href="{urlFor name="tag-subscribe" options="label.$tag|subscribe.true"}">
                <button class="btn btn-primary" title="Save the tag to a list of subscribed tags">Subscribe to Tag</button>
            {/if}
            </a>
        {/if}

		{if isset($tasks)}
			<div id="tasks">
				{foreach from=$tasks item=task}
					{include file="task.summary-link.tpl" task=$task}
				{/foreach}
			</div>
		{else}
			<div class="alert alert-warning">
				<strong>No open tasks</strong> Sorry, there are currently no open tasks for this label.
			</div>
		{/if}
	</div>

    <div class="span4 pull-right">
        {if isset($user)}
            {include file="tags.user-tags.inc.tpl"}
        {/if}

		{include file="tags.top-list.inc.tpl"}
	</div>
</div>

{include file="footer.tpl"}
