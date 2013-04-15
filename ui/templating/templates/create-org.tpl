{include file="header.tpl"}

<h1 class="page-header">
    Create an Organisation
    <small>
        Create your own organisation
    </small>
</h1>

    <form method='post' action="{urlFor name="create-org"}" class='well'>
        <table>
            <tr valign="top" align="center"> 
                <td width="50%">
                    
                    <label for='orgName'><strong>Organisation Name:</strong></label>
                    <input type='text' name='orgName' id='orgName' style="width: 80%"/>
                    
                    <label for='address'><strong>Address:</strong></label>
                    <textarea name='address' cols='40' rows='7' style="width: 80%"></textarea>
                    
                    <label for='city'><strong>City:</strong></label>
                    <input type='text' name='city' id='city' style="width: 80%" />

                    <label for='country'><strong>Country:</strong></label>
                    <input type='text' name='country' id='country' style="width: 80%" />
                    
                </td>
                <td width="50%">
                    
                    <label for='homepage'><strong>Home Page:</strong></label>
                    <input type='text' name='homepage' id='homepage' style="width: 80%" /> 
                    
                    <label for='email'><strong>E-Mail:</strong></label>
                    <input type='text' name='email' id='email' style="width: 80%" />   
                    
                    <label for='biography'><strong>Biography:</strong></label>
                    <textarea name='biography' cols='40' rows='10' style="width: 80%"></textarea>
                    
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
                            <td style="width: 15%"><input id="africa" name="africa" type="checkbox" /></td>   
                            <td style="width: 15%"><input id="asia" name="asia" type="checkbox" /></td> 
                            <td style="width: 15%"><input id="australia" name="australia" type="checkbox" /></td> 
                            <td style="width: 15%"><input id="europe" name="europe" type="checkbox" /></td> 
                            <td style="width: 15%"><input id="northAmerica" name="northAmerica" type="checkbox" /></td> 
                            <td style="width: 15%"><input id="southAmerica" name="southAmerica" type="checkbox" /></td> 
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