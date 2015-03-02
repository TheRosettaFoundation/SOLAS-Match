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

{if isset($nameErr)}
    <tr>
        <td colspan="2">
            <div class="alert alert-error">
                <h3>{Localisation::getTranslation('common_please_correct_errors')}</h3>
                    <ol>
                        <li>{$nameErr} {Localisation::getTranslation('common_invalid_characters')}</li>
                    </ol>
            </div> 
        </td>
    </tr>
{/if}
<h2>{Localisation::getTranslation('tag_list_tag_search')} <small>{Localisation::getTranslation('tag_list_1')}</small></h2>
<form method="post" action="{urlFor name="tags-list"}" class="well" accept-charset="utf-8">
    <p>{Localisation::getTranslation('tag_list_2')}</p>
    <input type="text" name="searchName" 
            value="{if isset($searchedText)}{$searchedText}{/if}" />
    <div>
        <button class="btn btn-primary" type="submit" name="search">
        	<i class="icon-search icon-white"></i>
        	{Localisation::getTranslation('tag_list_search')}
        </button>
        <button class="btn btn-inverse" type="submit" name="listAll">
        	<i class="icon-list icon-white"></i>
        	{Localisation::getTranslation('common_list_all')}
        </button>
    </div>
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
