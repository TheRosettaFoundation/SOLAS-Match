{include file="header.tpl"}
{include file="handle-flash-messages.tpl"}

    <div class="page-header">
        <h1>{sprintf(Localisation::getTranslation('tag_tasks_related_to'), TemplateHelper::uiCleanseHTML($tag->getLabel()))} <small>{Localisation::getTranslation('tag_0')}</small>
             {if isset($user)}
                {if !isset($sesskey)}{assign var="sesskey" value="0"}{/if}
                {if isset($subscribed)}
                    <a href="{urlFor name="tag-subscribe" options="id.{$tag->getId()}|subscribe.false|sesskey.{$sesskey}"}" class="pull-right btn btn-inverse"
                        title="{Localisation::getTranslation('tag_1')}">
                        <i class="icon-ban-circle icon-white"></i> {Localisation::getTranslation('tag_unsubscribe')}
                    </a>
                {else}
                    <a href="{urlFor name="tag-subscribe" options="id.{$tag->getId()}|subscribe.true|sesskey.{$sesskey}"}" class="pull-right btn btn-primary"
                        title="{Localisation::getTranslation('tag_2')}">
                        <i class="icon-ok-circle icon-white"></i> {Localisation::getTranslation('tag_subscribe_to_tag')}
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
                    <strong>{Localisation::getTranslation('tag_no_open_tasks')}</strong> {Localisation::getTranslation('tag_3')}
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
