{*
* 	Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*}

{l s='PickUp Station' mod='pickup'}: {$data.name|escape:'htmlall':'UTF-8'} <br>
{l s='Address' mod='pickup'}: {$data.address|escape:'htmlall':'UTF-8'}, {$data.postal|escape:'htmlall':'UTF-8'}, {$data.city|escape:'htmlall':'UTF-8'}, {$data.state|escape:'htmlall':'UTF-8'}, {$data.country|escape:'htmlall':'UTF-8'}<br>
{if $data.landmark}
{l s='Landmark' mod='pickup'}: {$data.landmark|escape:'htmlall':'UTF-8'}
{/if}
{l s='Phone' mod='pickup'}: {$data.phone|escape:'htmlall':'UTF-8'} <br>
{l s='Email' mod='pickup'}: {$data.email|escape:'htmlall':'UTF-8'}

