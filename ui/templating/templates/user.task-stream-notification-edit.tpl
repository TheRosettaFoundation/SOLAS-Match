{include file="header.tpl"}

<div class="page-header">
    <h1>
        <img src="http://www.gravatar.com/avatar/{md5( strtolower( trim($user->getEmail())))}?s=80&r=g" alt="" />
        {$user->getDisplayName()}
        <small>Select when to receive task stream emails</small>
    </h1>
</div>

{include file="handle-flash-messages.tpl"}

{assign var="user_id" value=$user->getUserId()}
<form method="post" action="{urlFor name="stream-notification-edit" options="user_id.$user_id"}">
    <p>
        This notification will periodically send you a list of the tasks that are most suited to your skills
        and areas of interest.
    </p>
    {if isset($interval)}
        <p>
            You are currently receiving emails <strong>{$interval}</strong>.
            {if $lastSent != null}
                The last email was sent on {$lastSent}.
            {/if}
        </p>
    {else}
        <p>You are not currently receiving task stream notifications</p>
    {/if}
    <p>I would like to receive this email notification
        <select name="interval">
            <option value="0"
                {if !isset($intervalId)}
                    selected="true"
                {/if}
            >
                never
            </option>
            <option value="{NotificationIntervalEnum::DAILY}"
                {if isset($intervalId) && $intervalId == NotificationIntervalEnum::DAILY}
                    selected="true"
                {/if}
            >
                daily
            </option>
            <option value="{NotificationIntervalEnum::WEEKLY}"
                {if isset($intervalId) && $intervalId == NotificationIntervalEnum::WEEKLY}
                    selected="true"
                {/if}
            >
                weekly
            </option>
            <option value="{NotificationIntervalEnum::MONTHLY}"
                {if isset($intervalId) && $intervalId == NotificationIntervalEnum::MONTHLY}
                    selected="true"
                {/if}
            >
                monthly
            </option>
        </select>
    </p>
    <input type="submit" value="Submit" class="btn btn-primary" />
</form>

{include file="footer.tpl"}
