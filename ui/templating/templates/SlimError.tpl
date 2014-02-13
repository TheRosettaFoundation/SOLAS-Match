{include file="header.tpl"}

<h1 class="page-header">{Localisation::getTranslation('slim_error_Oops_there_has')}</h1>

<p>
    {Localisation::getTranslation('slim_error_1')}<br />
	{Localisation::getTranslation('slim_error_2')}
</p>
<br />

<p>
    {Localisation::getTranslation('slim_error_3')} 
    <a href="{$referrer}">
        {Localisation::getTranslation('slim_error_4')}
    </a>
</p>
<br />

<div id="accordionSlimErrorMsg">
    <h3>{Localisation::getTranslation('slim_error_5')}</h3>
    <div name="adminList">
        <table class="table table-striped-left">
            <thead>
                <th><h3>Slim Application Error</h3></th>
            </thead>
            <tr>
                <td>
        		    <p><b>Message:</b>{$exception->getMessage()}<br />
		            <b>Loc:	</b>{$exception->getFile()}<br />
                    <b>Line: </b>{$exception->getLine()}<br />
                    <b>Code: </b>{$exception->getCode()}<br />
                    <br />
                    <b><u>Trace: </u></b><br />{$trace}<br />
                </td>
            </tr>
	  </table>
    </div>
</div>

{include file="footer.tpl"}
