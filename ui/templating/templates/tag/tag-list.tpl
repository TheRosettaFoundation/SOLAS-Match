{include file="header.tpl"}

<div class="page-header">
    <h1>{Localisation::getTranslation('tag_list_tag_list')} <small>{sprintf(Localisation::getTranslation('tag_list_all_tags_in'), {Settings::get('site.name')})}</small></h1>
</div>

{if isset($user_tags)}
    <h2>{Localisation::getTranslation('tag_list_subscribed_tags')} <small>{Localisation::getTranslation('tag_list_0')}</small></h2>
    <p style="margin-bottom:10px;"></p>
    <ul class="nav nav-list unstyled">
    {foreach $user_tags as $tag}
        <li>
            {assign var="tag_label" value=$tag->getLabel()}
            {assign var="tagId" value=$tag->getId()}
            <p>
                <a class="label" href="{urlFor name="tag-details" options="id.$tagId"}">{$tag_label}</a>
            </p>
        </li>
    {/foreach}
    </ul>
    <p style="margin-bottom:40px;"/>
    <hr />
{/if}

<h2>{Localisation::getTranslation('tag_list_tag_search')} <small>{Localisation::getTranslation('tag_list_1')}</small></h2>
<form method="post" action="{urlFor name="tags-list"}" class="well" accept-charset="utf-8">
    <p>{Localisation::getTranslation('tag_list_2')}</p>
    <input type="text" name="searchName" 
            value="{if isset($searchedText)}{$searchedText}{/if}" />
    <p>
        <input type="submit" name="search" value="    {Localisation::getTranslation('tag_list_search')}" class="btn btn-primary" />
        <i class="icon-search icon-white" style="position:relative; right:75px; top:2px;"></i>
        <input type="submit" name="listAll" value="    {Localisation::getTranslation('common_list_all')}" class="btn btn-inverse" />
        <i class="icon-list icon-white" style="position:relative; right:75px; top:2px;"></i>
    </p>
</form>
<p style="margin-bottom:10px;"/>
{if isset($foundTags)}
    {if count($foundTags) > 0}
        <h3>{sprintf(Localisation::getTranslation('tag_list_successfully_found'), {count($foundTags)})}</h3>
        <ul class="nav nav-list unstyled">
        {foreach $foundTags as $tag}
            <li>
                {assign var="tag_label" value=$tag->getLabel()}
                {assign var="tagId" value=$tag->getId()}
                <p>
                    <a class="label" href="{urlFor name="tag-details" options="id.$tagId"}">{$tag_label}</a>
                </p>
            </li>
        {/foreach}
        </ul>
    {else}
        <p class="alert alert-error">{sprintf(Localisation::getTranslation('tag_list_3'), {$searchedText})}</p>
    {/if}
{/if}

{include file='footer.tpl'}
