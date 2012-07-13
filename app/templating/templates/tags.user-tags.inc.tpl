{if isset($user_tags) AND is_array($user_tags)}
    <h3><i class="icon-tags"></i>Tags You Have Subscribed To</h3>
    <ul class="nav nav-list unstyled">
        {foreach from=$user_tags item=tag}
            <li>
                <a class="tag" href="{urlFor name="tag-details" options="label.$tag"}"><span class="label">{$tag}</span></a>
            </li>
        {/foreach}
    </ul>
{/if}
