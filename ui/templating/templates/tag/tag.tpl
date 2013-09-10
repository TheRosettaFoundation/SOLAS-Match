{include file="header.tpl"}
{include file="handle-flash-messages.tpl"}

    <div class="page-header">
        <h1>{sprintf(Localisation::getTranslation(Strings::TAG_TASKS_RELATED_TO), {$tag->getLabel()})} <small>{Localisation::getTranslation(Strings::TAG_0)}</small>
             {if isset($user)}
                {if isset($subscribed)}
                    <a href="{urlFor name="tag-subscribe" options="id.{$tag->getId()}|subscribe.false"}" class="pull-right btn btn-inverse"
                        title="{Localisation::getTranslation(Strings::TAG_1)}">
                        <i class="icon-ban-circle icon-white"></i> {Localisation::getTranslation(Strings::TAG_UNSUBSCRIBE)}
                    </a>
                {else}
                    <a href="{urlFor name="tag-subscribe" options="id.{$tag->getId()}|subscribe.true"}" class="pull-right btn btn-primary"
                        title="{Localisation::getTranslation(Strings::TAG_2)}">
                        <i class="icon-ok-circle icon-white"></i> {Localisation::getTranslation(Strings::TAG_SUBSCRIBE_TO_TAG)}
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
                    <strong>{Localisation::getTranslation(Strings::TAG_NO_OPEN_TASKS)}</strong> {Localisation::getTranslation(Strings::TAG_3)}
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
