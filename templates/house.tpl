{strip}
<div class="six columns">
{if $Action == ""}
    <p>{$Houseinfo}</p>
    <ul{if $Graphstyle == "Y"} class="sword"{/if}>
    {if $Houseid == ""}
        <li> <a href="house.php?action=land">{$Aland}</a></li>
        <li> <a href="house.php?action=list">{$Alist}</a></li>
        <li> <a href="house.php?action=rent">{$Arent}</a></li>
    {/if}
    {if $Houseid > "0"}
        <li> <a href="house.php?action=my">{$Ahouse}</a></li>
        <li> <a href="house.php?action=build">{$Aworkshop}</a></li>
        <li> <a href="house.php?action=land">{$Aland}</a></li>
        <li> <a href="house.php?action=list">{$Alist}</a></li>
        <li> <a href="house.php?action=rent">{$Arent}</a></li>
    {/if}  
    </ul>
{/if}

{if $Action == "rent"}
    <table width="100%">
    <tr>
    <td width="10%"><b><u>{$Hnumber}</u></b></td>
    <td width="10%"><b><u>{$Hseller}</u></b></td>
    <td width="30%"><b><u>{$Hname}</u></b></td>
    <td width="10%"><b><u>{$Hsize}</u></b></td>
    <td width="20%"><b><u>{$Htype}</u></b></td>
    <td width="10%"><b><u>{$Hcost}</u></b></td>
    <td width="10%"><b><u>{$Hoption}</u></b></td>
    </tr>
    {section name=house loop=$Housesname}
        <tr>
        <td>{$Housesid[house]}</td>
        <td><a href="view.php?view={$Housesseller[house]}">{$Housesseller[house]}</a></td>
        <td>{$Housesname[house]}</td>
        <td>{$Housesbuild[house]}</td>
        <td>{$Housestype[house]}</td>
    <td>{$Housescost[house]}</td>
    <td>{$Houseslink[house]}</td>
        </tr>
    {/section}
    </table> <a href="house.php">{$Aback}</a>
    {$Message}
{/if}

{if $Action == "land"}
    <p>{$Landinfo}</p>
    <ul{if $Graphstyle == "Y"} class="sword"{/if}>
    <li><a href="house.php?action=land&amp;step=buy">{$Buya}</a> {$Cost}</li>
    <li><a href="house.php">{$Aback}</a></li>
    </ul>
{/if}

{if $Action == "build"}
    <p>{$Buildinfo} <b>{$Points}</b> {$Buildinfo2}:</p>
    {$Buildhouse}
    {$Buildbed}
    {$Buildwardrobe}
    {$Upgrade}
    {if $Step == "new"}
        <form method="post" action="house.php?action=build&amp;step=new&amp;step2=make">
        {$Hname} <input type="text" name="name" /><br />
        <input type="submit" value="{$Abuild}" /></form><br />
    {/if}
    {if $Step == "upgrade"}
        {$Upginfo}{$Upg_cost}{$Tcredits}{$Upg_adamant}{$Tadamantium}{$Upg_crystal}{$Tcrystal}{$Upg_meteor}{$Tmeteor}{$Upg_yew}{$Tyew}{$Upg_elm}{$Telm}<br />
        <form method="post" action="house.php?action=build&amp;step=upgrade&amp;step2=make">
        {$Upgrade3} <input type="text" name="points" size="5" /><br />
        <input type="submit" value="{$Awork}" /></form><br />
    {/if}
    <br /><br /><a href="house.php">{$Aback}</a>
{/if}

{if $Action == "list"}
    <table width="100%">
    <tr>
    <td width="10%"><b><u>{$Hnumber}</u></b></td>
    <td width="10%"><b><u>{$Howner}</u></b></td>
    <td width="10%"><b><u>{$Hlocator}</u></b></td>
    <td width="40%"><b><u>{$Hname}</u></b></td>
    <td width="10%"><b><u>{$Hsize}</u></b></td>
    <td width="20%"><b><u>{$Htype}</u></b></td>
    </tr>
    {section name=house loop=$Housesname}
        <tr>
        <td>{$Housesid[house]}</td>
        <td><a href="view.php?view={$Housesowner[house]}">{$Housesowner[house]}</a></td>
    <td>{$Locator[house]}</td>
        <td>{$Housesname[house]}</td>
        <td>{$Housesbuild[house]}</td>
        <td>{$Housestype[house]}</td>
        </tr>
    {/section}
    </table> <a href="house.php">{$Aback}</a>
{/if}

{if $Action == "my"}
    {if $Step == ""}
        {$Houseinfo}<br /><br />
        <table width="70%">
        <tr><td width="35%">{$Hname}: </td><td width="65%"> {$Name} {if $Owner == $Id} [<a href="house.php?action=my&amp;step=name">{$Cname}</a>] {/if}</td></tr>
        <tr><td>{$Howner}: </td><td> <a href="view.php?view={$Owner}">{$Owner}</a></td></tr>
        <tr><td>{$Hsize}: </td><td> {$Build}</td></tr>
        <tr><td>{$Lamount}: </td><td> {$Size}</td></tr>
        <tr><td>{$Frooms}: </td><td> {$Unused}</td></tr>
        <tr><td>{$Hvalue}: </td><td> {$Value} {$Housename}</td></tr>
        <tr><td>{$Hlocator}: </td><td> {$Locator}</td></tr>
        <tr><td>{$Ibedroom}: </td><td> {$Bedroom}</td></tr>
        <tr><td>{$Wamount}: </td><td> {$Wardrobe}</td></tr>
        <tr><td>{$Iamount}: </td><td> {$Items}</td></tr>
        </table><br/>
        {$Bedroomlink}
        {$Wardrobelink}
        {$Locatorlink}
        {$Sellhouse}
        {$Locleave}
        (<a href="house.php">{$Aback}</a>)
    {/if}
    {if $Step == "leave" && $Step2 == ""}
        {$Youwant}
        <form method="post" action="house.php?action=my&amp;step=leave&amp;step2=confirm">
            <input type="submit" value="{$Yes}" />
        </form>
    {/if}
    {if $Step == "sell"}
        {if $Step2 == ""}
            {$Sellinfo}<br />
            <form method="post" action="house.php?action=my&amp;step=sell&amp;step2=sell">
            <input type="submit" value="{$Asend}" /> {$Housesale} <input type="text" name="cost" />{$Goldcoins}
            </form>
        {/if}
        {if $Step2 == "sell"}
            {$Message}<br /><br />
            (<a href="house.php">{$Aback}</a>)<br />
        {/if}
    {/if}
    {if $Step == "locator"}
        {if $Step2 == ""}
            <form method="post" action="house.php?action=my&amp;step=locator&amp;step2=change">
        <select name="loc"><option value="add">{$Oadd}</option>
        <option value="delete">{$Odelete}</option></select> {$Second}<br />
        {$Lid2}: <input type="text" name="lid" size="5" value="{$Locid}" /><br />
        <input type="submit" value="{$Amake}" /></form><br />
    {/if}
    {$Message}
    {/if}
    {if $Step == "name"}
        <form method="post" action="house.php?action=my&amp;step=name&amp;step2=change">
        <input type="submit" value="{$Achange}" /> {$Ona}: <input type="text" name="name" />
        </form><br />
    {/if}
    {if $Step == "bedroom"}
        {$Bedinfo}<ul{if $Graphstyle == "Y"} class="sword"{/if}>
        <li> <a href="house.php?action=my&amp;step=bedroom&amp;step2=rest">{$Arest}</a></li>
        <li> <a href="logout.php?rest=Y&amp;did={$Id}">{$Asleep}</a></li>
        </ul>
    {/if}
    {if $Step == "wardrobe"}
        {$Winfo} <b>{$Wardrobe} {$Wamount}</b> {$And2} <b>{$Amount} {$Iamount4}</b> {$Inw}<br /><ul>
        <li> <a href="house.php?action=my&amp;step=wardrobe&amp;step2=add">{$Ahidei}</a></li>
        <li> <a href="house.php?action=my&amp;step=wardrobe&amp;step2=list">{$Alist}</a></li>
        </ul>
       {if $Step2 == "list"}
           <table width="100%">
           <tr>
           <td width="28%"><b><u>{$Iname}</u></b></td>
           <td width="12%"><b><u>{$Ipower}</u></b></td>
           <td width="12%"><b><u>{$Idur}</u></b></td>
           <td width="12%"><b><u>{$Iagi}</u></b></td>
           <td width="12%"><b><u>{$Ispeed}</u></b></td>
           <td width="12%"><b><u>{$Iamount2}</u></b></td>
           <td width="12%"><b><u>{$Ioption}</u></b></td>
           </tr>
           {section name=house1 loop=$Itemname}
               <tr>
               <td>{$Itemname[house1]}</td>
               <td align="center">{$Itempower[house1]}</td>
               <td align="center">{$Itemdur[house1]}/{$Itemmaxdur[house1]}</td>
               <td align="center">{$Itemagility[house1]}</td>
               <td align="center">{$Itemspeed[house1]}</td>
               <td align="center">{$Itemamount[house1]}</td>
               <td>- <a href="house.php?action=my&amp;step=wardrobe&amp;take={$Itemid[house1]}">{$Aget}</a></td>
               </tr>
           {/section}
           </table>
        {/if}
        {if $Take != ""}
            {if $Step3 == ""}
                <form method="post" action="house.php?action=my&amp;step=wardrobe&amp;take={$Id}&amp;step3=add">
                <input type="submit" value="{$Aget}" /> {$Fromh} <input type="text" name="amount" value="{$Amount}" size="5" /> {$Amount2} <b>{$Name}</b><br />
                </form>
            {/if}
        {/if}
        {if $Step2 == "add"}
            <form method="post" action="house.php?action=my&amp;step=wardrobe&amp;step2=add&amp;step3=add"><table>
            <tr><td colspan="2">{$Item}: <select name="przedmiot">
            {section name=house2 loop=$Itemname1}
                <option value="{$Itemid1[house2]}">({$Iamount3}: {$Itemamount1[house2]}) {$Itemname1[house2]}</option>
            {/section}
            </select> {$Amount2} <input type="text" name="amount" size="5" /></td></tr>
            <tr><td colspan="2" align="center"><input type="submit" value="{$Ahide}" /></td></tr>
            </table></form>
        {/if}
    {/if}
    {if $Step != "" && $Step2 != "sell"}
        <br />(<a href="house.php?action=my">{$Aback}</a>)
    {/if}
{/if}
{/strip}
