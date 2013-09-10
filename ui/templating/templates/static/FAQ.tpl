{include file="header.tpl"}

<div>    
    <div id="title" style="margin-top: 20px; text-align: center">
        <h1>{Localisation::getTranslation(Strings::FAQ_FAQ)}</h1>
    </div>
    
    <div id="quicklinks" style="margin-top: 20px;text-align: center"   >
        <h2>{Localisation::getTranslation(Strings::FAQ_QUICKLINKS)}</h2>
        <h3 ><a href="#volunteerQuestions">{Localisation::getTranslation(Strings::FAQ_VOLUNTEERS)}</a><span style="white-space: pre">      |    </span><a href="#orgQuestions">{Localisation::getTranslation(Strings::COMMON_ORGANISATIONS)}</a></h3>
    </div>
    
    <div style="margin-top: 25px;text-align: justify" class="well">
    
        <div id="volunteerQuestions">

            <h2>{Localisation::getTranslation(Strings::FAQ_VOLUNTEER_QUESTIONS)}</h2>
            <hr />

            <p><a href="#vol1">{Localisation::getTranslation(Strings::FAQ_V_Q1)}</a></p>
            <p><a href="#vol2">{Localisation::getTranslation(Strings::FAQ_V_Q2)}</a></p>
            <p><a href="#vol3">{Localisation::getTranslation(Strings::FAQ_V_Q3)}</a></p>
            <p><a href="#vol4">{Localisation::getTranslation(Strings::FAQ_V_Q4)}</a></p>
            <p><a href="#vol5">{Localisation::getTranslation(Strings::FAQ_V_Q5)}</a></p>
            <p><a href="#vol6">{sprintf(Localisation::getTranslation(Strings::FAQ_V_Q6), {Settings::get("site.name")})}</a></p>
            <p><a href="#vol7">{Localisation::getTranslation(Strings::FAQ_V_Q7)}</a></p>   
            <hr />
        </div>

        <div id="volunteerAnswers">
            <h3 id="vol1">{Localisation::getTranslation(Strings::FAQ_V_Q1)}</h3>
            <p>{Localisation::getTranslation(Strings::FAQ_V_A1)}</p>

            <h3 id="vol2">{Localisation::getTranslation(Strings::FAQ_V_Q2)}</h3>
            <p>{sprintf(Localisation::getTranslation(Strings::FAQ_V_A2), {Settings::get("site.system_email_address")}, {Settings::get("site.name")})}</p>

            <h3 id="vol3">{Localisation::getTranslation(Strings::FAQ_V_Q3)}</h3>
            <p>{sprintf(Localisation::getTranslation(Strings::FAQ_V_A3), {Settings::get("site.system_email_address")})}</p>

            <h3 id="vol4">{Localisation::getTranslation(Strings::FAQ_V_Q4)}</h3>
            <p>{Localisation::getTranslation(Strings::FAQ_V_A4)}</p>

            <h3 id="vol5">{Localisation::getTranslation(Strings::FAQ_V_Q5)}</h3>
            <p>{sprintf(Localisation::getTranslation(Strings::FAQ_V_A5), {Settings::get("site.system_email_address")})}</p>

            <h3 id="vol6">{sprintf(Localisation::getTranslation(Strings::FAQ_V_Q6), {Settings::get("site.name")})}</h3>
            <p>{sprintf(Localisation::getTranslation(Strings::FAQ_V_A6), {Settings::get("site.name")})}</p>

            <h3 id="vol7">{Localisation::getTranslation(Strings::FAQ_V_Q7)}</h3>
            <p>{Localisation::getTranslation(Strings::FAQ_V_A7)}</p>

            <ol>
                <li>{sprintf(Localisation::getTranslation(Strings::FAQ_V_A7_ALL), {Settings::get("site.name")})}</li>
                <li>{Localisation::getTranslation(Strings::FAQ_V_A7_STRICT)}</li>
            </ol>

            <p>{Localisation::getTranslation(Strings::FAQ_V_A7_2)}</p>
        </div>
    </div>   
 <div style="margin-top: 20px;text-align: justify" class="well">
    <div id="orgQuestions">
        <h2>{Localisation::getTranslation(Strings::FAQ_ORGANISATION_QUESTIONS)}</h2>
        <hr />
        <p><a href="#org1">{Localisation::getTranslation(Strings::FAQ_O_Q1)}</a></p>
        <p><a href="#org2">{Localisation::getTranslation(Strings::FAQ_O_Q2)}</a></p>
        <p><a href="#org3">{Localisation::getTranslation(Strings::FAQ_O_Q3)}</a></p>
        <p><a href="#org4">{Localisation::getTranslation(Strings::FAQ_O_Q4)}</a></p>
        <p><a href="#org5">{Localisation::getTranslation(Strings::FAQ_O_Q5)}</a></p>
        <hr />
    </div>
    
    <div id="orgAnswers">
        <h3 id="org1">{Localisation::getTranslation(Strings::FAQ_O_Q1)}</h3>
        <p>{Localisation::getTranslation(Strings::FAQ_O_A1)}</p>
        
        <h3 id="org2">{Localisation::getTranslation(Strings::FAQ_O_Q2)}</h3>
        <p>{Localisation::getTranslation(Strings::FAQ_O_A2)}</p>
        
        <h3 id="org3">{Localisation::getTranslation(Strings::FAQ_O_Q3)}</h3>
        <p>{Localisation::getTranslation(Strings::FAQ_O_A3)}</p>
        
        <h3 id="org4">{Localisation::getTranslation(Strings::FAQ_O_Q4)}</h3>
        <p>{sprintf(Localisation::getTranslation(Strings::FAQ_O_A4), {Settings::get("site.name")})}</p>
        
        <h3 id="org5">{Localisation::getTranslation(Strings::FAQ_O_Q5)}</h3>
        <p>{Localisation::getTranslation(Strings::FAQ_O_A5)}</p>
    </div>
    </div>
</div>
{include file="footer.tpl"}