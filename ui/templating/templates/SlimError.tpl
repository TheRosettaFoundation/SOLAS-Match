
{include file="header.tpl"}

<br><br>
<h1>{Localisation::getTranslation(Strings::SLIM_ERROR_OOPS_THERE_HAS)}</h1><br><br>

<p>	{Localisation::getTranslation(Strings::SLIM_ERROR_1)}<br>
	{Localisation::getTranslation(Strings::SLIM_ERROR_2)}</p>
<br>
<p>	{Localisation::getTranslation(Strings::SLIM_ERROR_3)} <a href="{$req->getReferrer()}">{Localisation::getTranslation(Strings::SLIM_ERROR_4)}</a></p>

<br>
<div id="accordionSlimErrorMsg">
    <h3>{Localisation::getTranslation(Strings::SLIM_ERROR_5)}</h3>
    <div name="adminList">
        <table class="table table-striped-left">
            <thead>
                <th><h3>Slim Application Error</h3></th>
            </thead>
            <tr>
                <td>                
		  <b>Message: 	</b> 	{$exception->getMessage()} 	<br>
		  <b>Loc: 	</b>  	{$exception->getFile()}		<br>
		  <b>Line: 	</b> 	{$exception->getLine()}		<br>
		  <b>Code: 	</b> 	{$exception->getCode()}		<br>
		  <br>
		  <b><u>Trace: </u></b><br><br>	{$trace}<br>

                </td>
            </tr>
	  </table>
    </div>
</div>
<br><br><br><br><br>

{include file="footer.tpl"}