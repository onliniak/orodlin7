{strip}
<div class="six columns">
{$Bankinfo}
<ul {if $Graphstyle=="Y"}class="sword"{/if}>
<li><a href="bank.php?action=astral&amp;type=p">{$Aastral}</a></li>
<li><a href="bank.php?action=astral&amp;type=c">{$Aastral2}</a></li>
{$Safebox}
</ul>

{if $Action != "astral"}

    <form method="post" action="bank.php?action=withdraw">
    {$Iwant} <input type="submit" value="{$Awithdraw}" /> <input type="text" value="{$Bank}" name="with" /> {$Goldcoins}
    </form>

    <form method="post" action="bank.php?action=deposit">
    {$Iwant} <input type="submit" value="{$Adeposit}" /> <input type="text" value="{$Gold}" name="dep" /> {$Goldcoins}
    </form>

    <form method="post" action="bank.php?action=donation">
    {$Iwant} <input type="submit" value="{$Agive}" /> {$Dplayer} <input type="text" name="pid" size="3" /> <input type="text" value="{$Bank}" name="with" /> {$Goldcoins}
    </form>

    {if $Mithril > 0}
        <form method="post" action="bank.php?action=mithril">
        {$Iwant} <input type="submit" value="{$Agive}" /> {$Dplayer} <input type="text" name="pid" size="3" /> <input type="text" name="mithril" value="{$Mithril}" /> {$Mamount}.
        </form>
    {/if}

    {if $Items == 1}
        <form method="post" action="bank.php?action=items">
        {$Iwant} <input type="submit" value="{$Agive}" /> {$Dplayer} <input type="text" name="pid" size="3" />
        <input type="text" name="amount" size="3" /> {$Iamount} <select name="item">
        {section name=items loop=$Itemid}
            <option value="{$Itemid[items]}">({$Iamount2}: {$Itemamount[items]}) {$Itemname[items]} (+{$Itempower[items]}{if $Itemagi[items] != 0} {$Iagi} {$Itemagi[items]}{/if}{if $Itemspeed[items] != 0} {$Ispe} +{$Itemspeed[items]}{/if})</option>
        {/section}
        </select>.
        </form>
    {/if}

    {if $Potions == 1}
        <form method="post" action="bank.php?action=potions">
        {$Iwant} <input type="submit" value="{$Agive}" /> {$Dplayer} <input type="text" name="pid" size="3" />
        <input type="text" name="amount" size="3" /> {$Iamount} <select name="item">
        {section name=potion loop=$Potionid}
            <option value="{$Potionid[potion]}">({$Iamount2}: {$Potionamount[potion]}) {$Potionname[potion]} (+{$Potionpower[potion]})</option>
        {/section}
        </select>.
        </form>
    {/if}

    {if $Herbs == 1}
        <form method="post" action="bank.php?action=herbs">
        {$Iwant} <input type="submit" value="{$Agive}" /> {$Dplayer} <input type="text" name="pid" size="3" /> <select name="item">
        {section name=herb loop=$Herbname}
            <option value="{$Herbname[herb]}">{$Herbname2[herb]}</option>
        {/section}
        </select> {$Hamount} <input type="text" name="amount" size="5" />.
        </form>
    {/if}

    {if $Minerals == 1}
        <form method="post" action="bank.php?action=minerals">
        {$Iwant} <input type="submit" value="{$Agive}" /> {$Dplayer} <input type="text" name="pid" size="3" /> <select name="item">
        {section name=mineral loop=$Minname}
            <option value="{$Minoption[mineral]}">{$Minname[mineral]}</option>
        {/section}
        </select> {$Hamount} <input type="text" name="amount" size="5" />.
        </form>
    {/if}

    {$Crime}

{/if}

{if $Action == 'astral'}
    <br />{$Message}<br />

    {if $Type == "p"}
        <form method="post" action="bank.php?action=astral&amp;type=p&amp;step=piece">
            {$Tsend} <input type="text" name="pid" size="5" /> {$Tpiece} <select name="name">
                {section name=giveastral loop=$Tcomponents}
                    <option value="{$smarty.section.giveastral.index}">{$Tcomponents[giveastral]}</option>
                {/section}
            </select><br />
            {$Tnumber} <input type="text" name="number" size="5" /><br />
            {$Tamount} <input type="text" name="amount" size="5" /><br />
            <input type="submit" value="{$Agive}" />
        </form><br />

        <table align="center" width="100%">
            <tr>
                <td width="40%"><b><u>{$Tname}</u></b></td>
                {section name=astral loop=6 start=1}
                    <td align="center" width="4%"><b><u>{$smarty.section.astral.index}</u></b></td>
                {/section}
            </tr>
            <tr>
                <td colspan="17">{$Tmaps}</td>
            </tr>
            {section name=astral2 loop=$Mapsname}
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;{$Mapsname[astral2]}</td>
                    {section name=astral3 loop=5 start=0}
                        <td align="center">{$Mapsamount[astral2][astral3]}</td>
                    {/section}
                    <td colspan="10"></td>
                </tr>
            {/section}
            <tr>
                <td colspan="17"><hr /></td>
            </tr>
            <tr>
                <td width="40%"><b><u>{$Tname}</u></b></td>
                {section name=astral loop=16 start=1}
                    <td align="center" width="4%"><b><u>{$smarty.section.astral.index}</u></b></td>
                {/section}
            </tr>
            <tr>
                <td colspan="17">{$Tplans}</td>
            </tr>
            {section name=astral4 loop=$Plansname}
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;{$Plansname[astral4]}</td>
                    {section name=astral5 loop=15 start=0}
                        <td align="center">{$Plansamount[astral4][astral5]}</td>
                    {/section}
                </tr>
            {/section}
            <tr>
                <td colspan="17"><hr /></td>
            </tr>
            <tr>
                <td width="40%"><b><u>{$Tname}</u></b></td>
                {section name=astral loop=16 start=1}
                    <td align="center" width="4%"><b><u>{$smarty.section.astral.index}</u></b></td>
                {/section}
            </tr>
            <tr>
                <td colspan="17">{$Trecipes}</td>
            </tr>
            {section name=astral6 loop=$Recipesname}
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;{$Recipesname[astral6]}</td>
                    {section name=astral7 loop=15 start=0}
                        <td align="center">{$Recipesamount[astral6][astral7]}</td>
                    {/section}
                </tr>
            {/section}
            <tr>
                <td colspan="17"><hr /></td>
            </tr>
            <tr>
                <td width="40%"><b><u>{$Tname}</u></b></td>
                {section name=astral loop=16 start=1}
                    <td align="center" width="4%"><b><u>{$smarty.section.astral.index}</u></b></td>
                {/section}
            </tr>
            <tr>
                <td colspan="17">{$Tformulas}</td>
            </tr>
            {section name=astral101 loop=$Formulasname}
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;{$Formulasname[astral101]}</td>
                    {section name=astral102 loop=15 start=0}
                        <td align="center">{$Formulasamount[astral101][astral102]}</td>
                    {/section}
                </tr>
            {/section}
            <tr>
                <td colspan="17"><hr /></td>
            </tr>
            {*Astral warrior -complete maps*} 
            <tr>
                <td width="40%"><b><u>{$Tname}</u></b></td>
                {section name=astral loop=1 start=1}
                    <td align="center" width="4%"><b><u>{$smarty.section.astral.index}</u></b></td>
                {/section}
            </tr>
            <tr>
                <td colspan="17">{$Tmaps2}</td>
            </tr>
            {section name=astral8 loop=$Mapsname2}
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;{$Mapsname2[astral8]}</td>
                    <td align="center">{$Mapsamount2[astral8]}</td>
                </tr>
            {/section}
            <tr>
                <td colspan="17"><hr /></td>
            </tr>
            {*Astral craftsman -complete plans*} 
            <tr>
                <td width="40%"><b><u>{$Tname}</u></b></td>
                {section name=astral loop=1 start=1}
                    <td align="center" width="4%"><b><u>{$smarty.section.astral.index}</u></b></td>
                {/section}
            </tr>
            <tr>
                <td colspan="17">{$Tplans2}</td>
            </tr>
            {section name=astral9 loop=$Plansname2}
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;{$Plansname2[astral9]}</td>
                    <td align="center">{$Plansamount2[astral9]}</td>
                </tr>
            {/section}
            <tr>
                <td colspan="17"><hr /></td>
            </tr>
            {*Astral alchemist -complete recipes*} 
            <tr>
                <td width="40%"><b><u>{$Tname}</u></b></td>
                {section name=astral loop=1 start=1}
                    <td align="center" width="4%"><b><u>{$smarty.section.astral.index}</u></b></td>
                {/section}
            </tr>
            <tr>
                <td colspan="17">{$Trecipes2}</td>
            </tr>
            {section name=astral10 loop=$Recipesname2}
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;{$Recipesname2[astral10]}</td>
                    <td align="center">{$Recipesamount2[astral10]}</td>
                </tr>
            {/section}
            <tr>
                <td colspan="17"><hr /></td>
            </tr>
            {*Astral jeweller -complete formulas*}            
            <tr>
                <td width="40%"><b><u>{$Tname}</u></b></td>
                {section name=astral loop=1 start=1}
                    <td align="center" width="4%"><b><u>{$smarty.section.astral.index}</u></b></td>
                {/section}
            </tr>
            <tr>
                <td colspan="17">{$Tformulas2}</td>
            </tr>
            {section name=astral0700 loop=$Formulasname2}
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;{$Formulasname2[astral0700]}</td>
                    <td align="center">{$Formulasamount2[astral0700]}</td>
                </tr>
            {/section}
            <tr>

        </table>
    {/if}

    {if $Type == "c"}
        <form method="post" action="bank.php?action=astral&amp;type=c&amp;step=component">
            {$Tsend} <input type="text" name="pid" size="5" /> {$Tcomponent3} <select name="name">
                {section name=giveastral2 loop=$Tcomponents2}
                    <option value="{$smarty.section.giveastral2.index}">{$Tcomponents2[giveastral2]}</option>
                {/section}
            </select><br />
            <input type="hidden" name="number" value="1" />
            {$Tamount} <input type="text" name="amount" size="5" /><br />
            <input type="submit" value="{$Agive}" />
        </form><br />

        <table align="center" width="50%">
            <tr>
                <td width="80%"><b><u>{$Tname}</u></b></td>
                <td width="20%"></td>
            </tr>
            {section name=comp loop=$Tmagic}
                <tr>
                    <td colspan="2">{$Tmagic[comp]}</td>
                </tr>
                {section name=comp2 loop=$Tcomp[comp]}
                    <tr>
                        <td>&nbsp;&nbsp;&nbsp;{$Tcomp[comp][comp2]}</td>
                        <td>{$Components[comp][comp2]}</td>
                    </tr>
                {/section}
                <tr>
                    <td colspan="2"><hr /></td>
                </tr>
            {/section}
        </table>
    {/if}
{/if}
{/strip}
