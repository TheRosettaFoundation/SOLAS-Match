{* Must have url_name current_page and last_page assigned by parent *}
<div class="pagination pagination-centered">
<ul>
    {if $current_page > 1}
        {assign var="state" value=""}
    {else}
        {assign var="state" value="disabled"}
    {/if}
    <li class="{$state}">
        <a href="{urlFor name="$url_name" options="page_no.1"}" title="First">&lt;&lt;</a>
    </li>
    <li class="{$state}">
        {assign var="previous" value=($current_page -1)}
        <a href="{urlFor name="$url_name" options="page_no.$previous"}" title="Previous">&lt;</a>
    </li>
    <li>
        <a href="">Page {$current_page} of {$last_page}</a>
    </li>
    {if $current_page < $last_page}
        {assign var="state" value=""}
    {else}
        {assign var="state" value="disabled"}
    {/if}
    <li class="{$state}">
        {assign var="next" value=($current_page +1)}
        <a href="{urlFor name="$url_name" options="page_no.$next"}" title="Next">&gt;</a>
    </li>
    <li class="{$state}">
        <a href="{urlFor name="$url_name" options="page_no.$last_page"}" title="Last">&gt;&gt;</a>
    </li>
</ul>
</div>
