{include file="header.tpl"}

<h1 class="page-header">
    Create an Organisation
    <small>
        Create your own organisation
    </small><br/>
    <small>
        Note:
        <span style="color: red">*</span>
        denotes a required field.
    </small>
</h1>

    <form method='post' action="{urlFor name="create-org"}" class='well'>
        <table>
            {if isset($nameErr)}
                <tr>
                    <td colspan="2">
                        <div class="alert alert-error">
                            <h3>Please fill in all required fields:</h3>
                            <ol>
                                <li>{$nameErr}</li>
                            </ol>
                        </div> 
                    </td>
                </tr>
             {/if}
            <tr valign="top" align="center"> 
                <td width="50%">
                    
                    <label for='orgName'><strong>Organisation Name: <span style="color: red">*</span></strong></label>
                    <input type='text' name='orgName' id='orgName' style="width: 80%" {if isset($org)} value="{$org->getName()}" {/if}/>
                    
                    <label for='address'><strong>Address:</strong></label>
                    <textarea name='address' cols='40' rows='7' style="width: 80%">{if isset($org)} {$org->getAddress()} {/if}</textarea>
                    
                    <label for='city'><strong>City:</strong></label>
                    <input type='text' name='city' id='city' style="width: 80%" {if isset($org)} value="{$org->getCity()}" {/if}/>

                    <label for='country'><strong>Country:</strong></label>
                    <input type='text' name='country' id='country' style="width: 80%" {if isset($org)} value="{$org->getCountry()}" {/if}/>
                    
                </td>
                <td width="50%">
                    
                    <label for='homepage'><strong>Home Page:</strong></label>
                    <input type='text' name='homepage' id='homepage' style="width: 80%" {if isset($org)} value="{$org->getHomePage()}" {/if}/> 
                    
                    <label for='email'><strong>E-Mail:</strong></label>
                    <input type='text' name='email' id='email' style="width: 80%"{if isset($org)} value="{$org->getEmail()}" {/if}/>   
                    
                    <label for='biography'><strong>Biography:</strong></label>
                    <textarea name='biography' cols='40' rows='10' style="width: 80%">{if isset($org)} {$org->getBiography()} {/if}</textarea>
                    
                </td>
            </tr>
            <tr>                
                <td colspan="2" style="font-weight: bold; text-align: center; padding-bottom: 10px">
                    <hr/>
                    Regional Focus:
                </td>
            </tr>  
            <tr align="center">
                <td colspan="2">
                    <table> 
                        <thead>
                            <th>Africa</th>
                            <th>Asia</th>
                            <th>Australia</th>
                            <th>Europe</th>
                            <th>North America</th>
                            <th>South America</th>                       
                        </thead>
                        <tr align="center">
                            <td style="width: 15%"><input id="africa" name="africa" type="checkbox" {if strstr($org->getRegionalFocus(), "Africa")} checked {/if} /></td>   
                            <td style="width: 15%"><input id="asia" name="asia" type="checkbox" {if strstr($org->getRegionalFocus(), "Asia")} checked {/if} /></td> 
                            <td style="width: 15%"><input id="australia" name="australia" type="checkbox" {if strstr($org->getRegionalFocus(), "Australia")} checked {/if} /></td> 
                            <td style="width: 15%"><input id="europe" name="europe" type="checkbox" {if strstr($org->getRegionalFocus(), "Europe")} checked {/if} /></td> 
                            <td style="width: 15%"><input id="northAmerica" name="northAmerica" type="checkbox" {if strstr($org->getRegionalFocus(), "North-America")} checked {/if} /></td> 
                            <td style="width: 15%"><input id="southAmerica" name="southAmerica" type="checkbox" {if strstr($org->getRegionalFocus(), "South-America")} checked {/if} /></td> 
                        </tr>
                      
                    </table> 
                </td>
            </tr>
            <tr>                
                <td colspan="2" style="padding-bottom: 20px"><hr/></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <button type="submit" name="submit" value="createOrg" class="btn btn-success">
                        <i class="icon-star icon-white"></i> Create Organisation
                    </button>
                </td>
            </tr>
  
        </table>
    </form>

{include file="footer.tpl"}