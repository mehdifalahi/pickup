{*
* 	Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*}
<section class="box">
	<header><h4>{l s='PickUp Station' mod='pickup'}</h4></header>
	<div class="table-responsive">
		<table class="table">
			<tr>
				<td>{l s='Name' mod='pickup'}</td>
				<td>{$data.name|escape:'htmlall':'UTF-8'}</td>
			</tr>
			<tr>
				<td>{l s='Address' mod='pickup'}</td>
				<td>{$data.address|escape:'htmlall':'UTF-8'}, {$data.postal|escape:'htmlall':'UTF-8'}, {$data.city|escape:'htmlall':'UTF-8'}, {$data.state|escape:'htmlall':'UTF-8'}, {$data.country|escape:'htmlall':'UTF-8'}</td>
			</tr>
			{if $data.landmark}
			<tr>
				<td>{l s='Landmark' mod='pickup'}</td>
				<td>{$data.landmark|escape:'htmlall':'UTF-8'}</td>
			</tr>
			{/if}
			<tr>
				<td>{l s='Phone' mod='pickup'}</td>
				<td>{$data.phone|escape:'htmlall':'UTF-8'}</td>
			</tr>
			<tr>
				<td>{l s='Email' mod='pickup'}</td>
				<td>{$data.email|escape:'htmlall':'UTF-8'}</td>
			</tr>
		</table>
	</div>
</section>