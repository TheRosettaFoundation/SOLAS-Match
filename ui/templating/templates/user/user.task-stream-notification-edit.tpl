{include file="header.tpl"}

<div class="page-header">
    <h1>
        <img src="http://www.gravatar.com/avatar/{md5( strtolower( trim($user->getEmail())))}?s=80&r=g" alt="" />
        {$user->getDisplayName()}
        <small>Select how often you want to receive task stream e-mails.</small>
    </h1>
</div>

{if !(isset($strict))}
    {assign var="strict" value=false}
{/if}

{include file="handle-flash-messages.tpl"}

{assign var="user_id" value=$user->getId()}
<form method="post" action="{urlFor name="stream-notification-edit" options="user_id.$user_id"}">
    <p>
        This notification will periodically send you a list of the tasks that are most suited to your skills
        and areas of interest. If you select strict notifications then you will only receive notifications
        for tasks that match a language you have expressed an interest in (i.e. by setting it as your native
        language or as a secondary language).
    </p>
    {if isset($interval)}
        <p>
            You are currently receiving 
            {if $strict}
                <strong>strict</strong>
            {/if}
            notifications <strong>{$interval}</strong>.
            {if $lastSent != null}
                The last e-mail was sent on {$lastSent}.
            {/if}
        </p>
    {else}
        <p style="font-weight: bold">You are not currently receiving task stream notifications!</p>
    {/if}
    <p>I would like to receive 
        <select name="strictMode">
            <option value="disabled" {if (!$strict)}selected="true"{/if}>
                all
            </option>
            <option value="enabled" {if ($strict)}selected="true"{/if}>
                strict
            </option>
        </select>
        notification(s): 
        <select name="interval">
            <option value="0"
                {if !isset($intervalId)}
                    selected="true"
                {/if}
            >
                Never
            </option>
            <option value="{NotificationIntervalEnum::DAILY}"
                {if isset($intervalId) && $intervalId == NotificationIntervalEnum::DAILY}
                    selected="true"
                {/if}
            >
                Daily
            </option>
            <option value="{NotificationIntervalEnum::WEEKLY}"
                {if isset($intervalId) && $intervalId == NotificationIntervalEnum::WEEKLY}
                    selected="true"
                {/if}
            >
                Weekly
            </option>
            <option value="{NotificationIntervalEnum::MONTHLY}"
                {if isset($intervalId) && $intervalId == NotificationIntervalEnum::MONTHLY}
                    selected="true"
                {/if}
            >
                Monthly
            </option>
        </select>
    </p>
    <button type="submit" value="Submit" class="btn btn-success">
        <i class="icon-upload icon-white"></i> Submit
    </button>      
</form>

{include file="footer.tpl"}
