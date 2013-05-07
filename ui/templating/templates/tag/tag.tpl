{include file="header.tpl"}
{include file="handle-flash-messages.tpl"}

    <div class="page-header">
        <h1>Tasks related to "{$tag}" <small>Find tasks tagged with this tag</small>
             {if isset($user)}
                {if isset($subscribed)}
                    <a href="{urlFor name="tag-subscribe" options="label.$tag|subscribe.false"}" class="pull-right btn btn-inverse"
                        title="Remove tag from a list of tags you have subscribed to">
                        <i class="icon-ban-circle icon-white"></i> Unsubscribe
                    </a>
                {else}
                    <a href="{urlFor name="tag-subscribe" options="label.$tag|subscribe.true"}" class="pull-right btn btn-primary"
                        title="Save the tag to a list of subscribed tags">
                        <i class="icon-ok-circle icon-white"></i> Subscribe to Tag
                    </a>
                {/if}
            {/if}           
        </h1>
    </div>

    <div class="row">
        <div class="span8">
            {if isset($tasks)}
                <div id="tasks">
                    {foreach from=$tasks item=task}
                            {include file="task/task.summary-link.tpl" task=$task}
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
                {include file="tag/tags.user-tags.inc.tpl"}
            {/if}

            {include file="tag/tags.top-list.inc.tpl"}
        </div>
    </div>

{include file="footer.tpl"}
