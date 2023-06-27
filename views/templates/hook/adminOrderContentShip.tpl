{*
* 	Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*}
<h4><b>{l s='Pickup Station' mod='pickup'}:</b> {$data.name|escape:'htmlall':'UTF-8'}</h4>
<div>
	<p><label>{l s='Address' mod='pickup'}:</label> {$data.address|escape:'htmlall':'UTF-8'} ,{$data.postal|escape:'htmlall':'UTF-8'}, {$data.city|escape:'htmlall':'UTF-8'}, {$data.state|escape:'htmlall':'UTF-8'}, {$data.country|escape:'htmlall':'UTF-8'}<p>
	{if $data.landmark}
	<p><label>{l s='Landmark' mod='pickup'}:</label> {$data.landmark|escape:'htmlall':'UTF-8'}<p>
	{/if}
	<p><label>{l s='Phone' mod='pickup'}:</label> {$data.phone|escape:'htmlall':'UTF-8'}</p>
	<p><label>{l s='Email' mod='pickup'}:</label> {$data.email|escape:'htmlall':'UTF-8'}</p>
</div>

