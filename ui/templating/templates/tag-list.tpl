{include file="header.tpl"}

<div class="page-header">
    <h1>Tag List <small> All tags in the SOLAS Match System.</small></h1>
</div>

{if isset($user_tags)}
    <h2>Subscribed Tags <small>These tags are more likely to show up in your stream of tasks.</small></h2>
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
{/if}

<p style="margin-bottom:40px;"/>
<hr />

<h2>Tag Search <small>Search for tags in the system.</small></h2>
<form method="post" action="{urlFor name="tags-list"}" class="well">
    <p>Enter text to search for</p>
    <input type="text" name="searchName" 
            value="{if isset($searchedText)}{$searchedText}{/if}" />
    <p>
        <input type="submit" name="search" value="    Search" class="btn btn-primary" />
        <i class="icon-search icon-white" style="position:relative; right:75px; top:2px;"></i>
        <input type="submit" name="listAll" value="    List All" class="btn btn-inverse" />
        <i class="icon-list icon-white" style="position:relative; right:75px; top:2px;"></i>
    </p>
</form>
<p style="margin-bottom:10px;"/>
{if isset($foundTags)}
    {if count($foundTags) > 0}
        <h3>Successfully found {count($foundTags)} tag(s)</h3>
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
        <p class="alert alert-error">No tags matching tags for keyword <b>{$searchedText}</b>.</p>
    {/if}
{/if}

{include file='footer.tpl'}
