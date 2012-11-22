{include file="header.tpl"}

<h1 class="page-header">
    {$badge->getTitle()}
    <small>Edit organisation badge details</small>
</h1>

{* {assign var="badge_id" value=$badge->getBadgeId()} *}
<form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}" class="well">
    <input type="hidden" name="badge_id" value="{$badge->getBadgeId()}" />    
    <label for='title'>Badge Title</label>
    <input type='text' name='title' id='title'
    {if $badge->getTitle() != ''}
        value='{$badge->getTitle()}'
    {else}
        placeholder='Enter updated badge name here'
    {/if} /> 

    <label for="description">Description</label>
    <textarea name='description' cols='40' rows='5' {if $badge->getDescription() == ''} placeholder="Enter updated badge description here" {/if}
    >{if $badge->getDescription() != ''}{$badge->getDescription()}{/if}</textarea>
    
    <p>
        <button type='submit' class='btn btn-primary' name='submit'>
            <i class="icon-refresh icon-white"></i> Update
        </button>
    </p>
</form>

{include file="footer.tpl"}